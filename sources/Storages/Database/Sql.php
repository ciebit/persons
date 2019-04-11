<?php
namespace Ciebit\Persons\Storages\Database;

use Ciebit\Persons\Characteristics\EducationalLevel;
use Ciebit\Persons\Characteristics\Gender;
use Ciebit\Persons\Characteristics\MaritalStatus;
use Ciebit\Persons\Collection;
use Ciebit\Persons\Legal;
use Ciebit\Persons\Natural;
use Ciebit\Persons\Person;
use Ciebit\Persons\Status;
use Ciebit\Persons\Storages\Storage;
use Ciebit\SqlHelper\Sql as SqlHelper;
use DateTime;
use Exception;
use PDO;

use function array_column;
use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function explode;
use function is_aray;
use function intval;

class Sql implements Database
{
    /** @var string */
    private const COLUMN_BIRTH_DATE = 'birth_date';

    /** @var string */
    private const COLUMN_DESCRIPTION = 'description';

    /** @var string */
    private const COLUMN_EDUCATIONAL_LEVEL = 'educational_level';

    /** @var string */
    private const COLUMN_FANTASY_NAME = 'nickname';

    /** @var string */
    private const COLUMN_FOUNDATION_DATE = 'birth_date';

    /** @var string */
    private const COLUMN_GENDER = 'gender';

    /** @var string */
    private const COLUMN_ID = 'id';

    /** @var string */
    private const COLUMN_IMAGE_ID = 'image_id';

    /** @var string */
    private const COLUMN_MARITAL_STATUS = 'marital_status';

    /** @var string */
    private const COLUMN_NAME = 'name';

    /** @var string */
    private const COLUMN_NICKNAME = 'nickname';

    /** @var string */
    private const COLUMN_SLUG = 'slug';

    /** @var string */
    private const COLUMN_STATUS = 'status';

    /** @var string */
    private const COLUMN_TYPE = 'type';

    static private $counterKey = 0;

    /** @var PDO */
    private $pdo;

    /** @var SqlHelper */
    private $sqlHelper;

    /** @var string */
    private $table;

    /** @var string */
    private $tableLabelAssociation;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->sqlHelper = new SqlHelper;
        $this->table = 'cb_persons';
        $this->totalItemsOfLastFindWithoutLimitations = 0;
    }

    public function __clone()
    {
        $this->sqlHelper = clone $this->sqlHelper;
    }

    private function addFilter(string $fieldName, int $type, string $operator, ...$value): self
    {
        $field = "`{$this->table}`.`{$fieldName}`";
        $this->sqlHelper->addFilterBy($field, $type, $operator, ...$value);
        return $this;
    }

    public function addFilterById(string $operator, string ...$ids): Storage
    {
        $ids = array_map('intval', $ids);
        $this->addFilter(self::COLUMN_ID, PDO::PARAM_INT, $operator, ...$ids);
        return $this;
    }

    public function addFilterByName(string $operator, string ...$name): Storage
    {
        $this->addFilter(self::COLUMN_NAME, PDO::PARAM_STR, $operator, ...$name);
        return $this;
    }

    public function addFilterBySlug(string $operator, string ...$slug): Storage
    {
        $this->addFilter(self::COLUMN_SLUG, PDO::PARAM_STR, $operator, ...$slug);
        return $this;
    }

    public function addFilterByStatus(string $operator, Status ...$status): Storage
    {
        $statusInt = array_map(function($status){
            return (int) $status->getValue();
        }, $status);
        $this->addFilter(self::COLUMN_STATUS, PDO::PARAM_INT, $operator, ...$statusInt);
        return $this;
    }

    public function addFilterByType(string $operator, string ...$type): Storage
    {
        $this->addFilter(self::COLUMN_TYPE, PDO::PARAM_STR, $operator, ...$type);
        return $this;
    }

    public function addOrderBy(string $field, string $direction): Storage
    {
        $this->sqlHelper->addOrderBy($field, $direction);
        return $this;
    }

    /** @throws Exception */
    public function create(array $data): Person
    {
        switch ($data[self::COLUMN_TYPE]) {
            case Legal::TYPE: return $this->createLegal($data);
            case Natural::TYPE: return $this->createNatural($data);
        }

        throw new Exception('ciebit.persons.storages.database.unidentifiedType', 1);
    }

    private function createLegal(array $data): Legal
    {
        list(
            $id, $imageId, $name, $slug, $description,
            $status, $fantasyName, $foundationDate
        ) = [
            (string) $data[self::COLUMN_ID],
            (string) $data[self::COLUMN_IMAGE_ID],
            (string) $data[self::COLUMN_NAME],
            (string) $data[self::COLUMN_SLUG],
            (string) $data[self::COLUMN_DESCRIPTION],
            new Status((int) $data[self::COLUMN_STATUS]),
            (string) $data[self::COLUMN_FANTASY_NAME],
            (string) $data[self::COLUMN_FOUNDATION_DATE]
        ];

        $person = (new Legal($name, $slug, $status))
        ->setImageId($imageId)
        ->setDescription($description)
        ->setFantasyName($fantasyName)
        ->setId($id);

        if ($foundationDate) {
            $person->setFoundationDate(new DateTime($foundationDate));
        }

        return $person;
    }

    private function createNatural(array $data): Natural
    {
        list(
            $id, $imageId, $name, $slug, $status, $nickname, $description,
            $birthDate, $educationalLevel, $gender, $maritalStatus
        ) = [
            (string) $data[self::COLUMN_ID],
            (string) $data[self::COLUMN_IMAGE_ID],
            (string) $data[self::COLUMN_NAME],
            (string) $data[self::COLUMN_SLUG],
            new Status((int) $data[self::COLUMN_STATUS]),
            (string) $data[self::COLUMN_NICKNAME],
            (string) $data[self::COLUMN_DESCRIPTION],
            (string) $data[self::COLUMN_BIRTH_DATE],
            (int) $data[self::COLUMN_EDUCATIONAL_LEVEL],
            (int) $data[self::COLUMN_GENDER],
            (int) $data[self::COLUMN_MARITAL_STATUS]
        ];

        $person = (new Natural($name, $slug, $status))
        ->setId($id)
        ->setImageId($imageId)
        ->setNickname($nickname)
        ->setDescription($description)
        ->setEducationalLevel(new EducationalLevel($educationalLevel))
        ->setGender(new Gender($gender))
        ->setMaritalStatus(new MaritalStatus($maritalStatus))
        ;

        if ($birthDate) {
            $person->setBirthDate(new DateTime($birthDate));
        }

        return $person;
    }

    private function destroy(Person $person): Storage
    {
        $statement = $this->pdo->prepare("
            DELETE FROM {$this->table} WHERE `id` = :id;
        ");
        $statement->bindValue(':id', (int) $person->getId(), PDO::PARAM_INT);
        $statement->execute();
        return $this;
    }

    /** @throws Exception */
    public function findAll(): Collection
    {
        $statement = $this->pdo->prepare("
            SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table}
            {$this->sqlHelper->generateSqlJoin()}
            WHERE {$this->sqlHelper->generateSqlFilters()}
            {$this->sqlHelper->generateSqlOrder()}
            {$this->sqlHelper->generateSqlLimit()}
        ");

        $this->sqlHelper->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.persons.storages.database.get_error', 2);
        }

        $this->updateTotalItemsWithoutFilters();

        $collection = new Collection;
        $personsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($personsData as $personData) {
            $person = $this->create($personData);
            $collection->add($person);
        }

        return $collection;
    }

    public function findOne(): ?Person
    {
        $storage = clone $this;
        $personCollection = $storage->setLimit(1)->findAll();

        if (count($personCollection) == 0) {
            return null;
        }

        return $personCollection->getArrayObject()->offsetGet(0);
    }

    private function getFields(): string
    {
        return "
            `{$this->table}`.`". self::COLUMN_ID ."`,
            `{$this->table}`.`". self::COLUMN_IMAGE_ID ."`,
            `{$this->table}`.`". self::COLUMN_BIRTH_DATE ."`,
            `{$this->table}`.`". self::COLUMN_DESCRIPTION ."`,
            `{$this->table}`.`". self::COLUMN_EDUCATIONAL_LEVEL ."`,
            `{$this->table}`.`". self::COLUMN_FANTASY_NAME ."`,
            `{$this->table}`.`". self::COLUMN_FOUNDATION_DATE ."`,
            `{$this->table}`.`". self::COLUMN_GENDER ."`,
            `{$this->table}`.`". self::COLUMN_ID ."`,
            `{$this->table}`.`". self::COLUMN_MARITAL_STATUS ."`,
            `{$this->table}`.`". self::COLUMN_NAME ."`,
            `{$this->table}`.`". self::COLUMN_NICKNAME ."`,
            `{$this->table}`.`". self::COLUMN_SLUG ."`,
            `{$this->table}`.`". self::COLUMN_STATUS ."`,
            `{$this->table}`.`". self::COLUMN_TYPE ."`
        ";
    }

    public function getTotalItemsOfLastFindWithoutLimitations(): int
    {
        return $this->totalItemsLastQuery;
    }

    private function save(Person $person): Storage
    {
        $statement = $this->pdo->prepare("
            SELECT * FROM {$this->table} WHERE id = :id;
        ");
        $statement->bindValue(':id', (int) $person->getId(), PDO::PARAM_INT);

        $execute = $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        !$data ?
        $this->store($person) :
        $this->update($person);
        return $this;
    }

    public function setLimit(int $limit): Storage
    {
        $this->sqlHelper->setLimit($limit);
        return $this;
    }

    public function setOffset(int $offset): Storage
    {
        $this->sqlHelper->setOffset($offset);
        return $this;
    }

    public function setTable(string $name): Database
    {
        $this->table = $name;
        return $this;
    }

    private function store(Person $person): Storage
    {
        if ($person instanceof Natural) {
            $statement = $this->pdo->prepare("
                INSERT INTO {$this->table} (`id`, `name`, `birthdate`, `type`, `status`)
                VALUES (:id, :name, :birthdate, :type, :status)
            ");
            $statement->bindValue(':birthdate', $person->getBirthDate()->format('Y-m-d'), PDO::PARAM_STR);
        } else {
            $statement = $this->pdo->prepare("
                INSERT INTO {$this->table} (`id`, `name`, `fantasy_name`, `foundation_date`, `type`, `status`)
                VALUES (:id, :name, :fantasy_name, :foundation_date, :type, :status)
            ");
            $statement->bindValue(':fantasy_name', $person->getFantasyName(), PDO::PARAM_STR);
            $statement->bindValue(':foundation_date', $person->getFoundationDate()->format('Y-m-d'), PDO::PARAM_STR);
        }
        $statement->bindValue(':id', (int) $person->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $person->getName(), PDO::PARAM_STR);
        $statement->bindValue(':status', $person->getStatus()->getValue(), PDO::PARAM_INT);
        $statement->bindValue(':type', $person->getType(), PDO::PARAM_STR);

        $statement->execute();

        return $this;
    }

    private function update(Person $person): Storage
    {
        if ($person instanceof Natural) {
            $statement = $this->pdo->prepare("
                UPDATE {$this->table}
                SET
                name = :name,
                birthdate = :birthdate,
                type = :type,
                status = :status
                WHERE id = :id;
            ");
            $statement->bindValue(':birthdate', $person->getBirthDate()->format('Y-m-d'), PDO::PARAM_STR);
        } else {
            $statement = $this->pdo->prepare("
                UPDATE {$this->table}
                SET
                name = :name,
                fantasy_name = :fantasy_name,
                foundation_date = :foundation_date,
                type = :type,
                status = :status
                WHERE id = :id;
            ");
            $statement->bindValue(':fantasy_name', $person->getFantasyName(), PDO::PARAM_STR);
            $statement->bindValue(':foundation_date', $person->getFoundationDate()->format('Y-m-d'), PDO::PARAM_STR);
        }

        $statement->bindValue(':id', (int) $person->getId(), PDO::PARAM_INT);
        $statement->bindValue(':name', $person->getName(), PDO::PARAM_STR);
        $statement->bindValue(':type', $person->getType(), PDO::PARAM_STR);
        $statement->bindValue(':status', $person->getStatus()->getValue(), PDO::PARAM_INT);

        $statement->execute();

        return $this;
    }

    private function updateTotalItemsWithoutFilters(): self
    {
        $this->totalItemsOfLastFindWithoutLimitations = $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
        return $this;
    }
}
