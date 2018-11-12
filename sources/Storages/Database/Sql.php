<?php
declare(strict_types=1);
namespace Ciebit\Persons\Storages\Database;

use Ciebit\Persons\Person;
use Ciebit\Persons\Legal;
use Ciebit\Persons\Natural;
use Ciebit\Persons\Collection;
use Ciebit\Persons\Enum\Status;
use Ciebit\Persons\Storages\Storage;
use Exception;
use DateTime;
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

class Sql extends SqlFilters implements Database
{
    static private $counterKey = 0;
    private $pdo; #PDO
    private $table; #string
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->table = 'cb_persons';
    }
    
    public function addFilterById(int $id, string $operator = '='): Storage
    {
        $key = 'id';
        $sql = "`persons`.`id` $operator :{$key}";
        $this->addfilter($key, $sql, PDO::PARAM_STR, $id);
        return $this;
    }
    
    public function addFilterByIds(string $operator, int ...$ids): Storage
    {
         $keyPrefix = 'id';
         $keys = [];
         $operator = $operator == '!=' ? 'NOT IN' : 'IN';
         foreach ($ids as $id) {
             $key = $keyPrefix . self::$counterKey++;
             $this->addBind($key, PDO::PARAM_STR, $id);
             $keys[] = $key;
         }
         $keysStr = implode(', :', $keys);
         $this->addSqlFilter("`persons`.`id` {$operator} (:{$keysStr})");
         return $this;
    }
    
    public function addFilterByName(string $name, string $operator = '='): Storage
    {
        $key = 'name';
        $sql = "`persons`.`name` $operator :{$key}";
        $this->addfilter($key, $sql, PDO::PARAM_STR, $name);
        return $this;
    }

    public function addFilterByPersonType(string $type, string $operator = '='): Storage
    {
        $key = 'type';
        $sql = "`persons`.`type` $operator :{$key}";
        $this->addfilter($key, $sql, PDO::PARAM_STR, $type);
        return $this;
    }
    
    public function addFilterByStatus(Status $status, string $operator = '='): Storage
    {
        $key = 'status';
        $sql = "`persons`.`status` {$operator} :{$key}";
        $this->addFilter($key, $sql, PDO::PARAM_INT, $status->getValue());
        return $this;
    }
    
    public function get(): ?Person
    {
        $statement = $this->pdo->prepare("
            SELECT
            {$this->getFields()}
            FROM {$this->table} as `persons`
            {$this->generateSqlJoin()}
            WHERE {$this->generateSqlFilters()}
            {$this->generateOrder()}
            LIMIT 1
        ");
        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.persons.storages.database.get_error', 2);
        }
        $personsData = $statement->fetch(PDO::FETCH_ASSOC);
        if ($personsData == false) {
            return null;
        }
        return $this->createPerson($personsData);
    }
    
    public function store(Person $person): Storage
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
    
    public function update(Person $person): Storage
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
    
    public function save(Person $person): Storage
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
    
    public function destroy(Person $person): Storage
    {
        $statement = $this->pdo->prepare("
            DELETE FROM {$this->table} WHERE `id` = :id;
        ");
        $statement->bindValue(':id', (int) $person->getId(), PDO::PARAM_INT);
        $statement->execute();
        return $this;
    }
    
    public function createPerson(array $data): Person
    {
        if($data['type'] == 'legal') {
            $person = (new Legal(
                $data['name'] ?? '',
                new Status((int) $data['status'] ?? Status::INACTIVE)
            ))
            ->setId($data['id'] ?? '')
            ->setFantasyName($data['fantasy_name'] ?? '');
            $data['foundation_date'] && $person->setFoundationDate(new DateTime($data['foundation_date']));

            return $person;
        }
        $person = (new Natural(
            $data['name'] ?? '',
            new Status((int) $data['status'] ?? Status::INACTIVE)
        ))
        ->setId($data['id'] ?? '');
        $data['birthdate'] && $person->setBirthDate(new DateTime($data['birthdate']));

        return $person;
    }
    
    public function getAll(): Collection
    {
        $statement = $this->pdo->prepare("
            SELECT SQL_CALC_FOUND_ROWS
            {$this->getFields()}
            FROM {$this->table} as `persons`
            {$this->generateSqlJoin()}
            WHERE {$this->generateSqlFilters()}
            {$this->generateOrder()}
            {$this->generateSqlLimit()}
        ");
        $this->bind($statement);
        if ($statement->execute() === false) {
            throw new Exception('ciebit.persons.storages.database.get_error', 2);
        }
        $collection = new Collection;
        $personsData = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($personsData as $personData) {
            $person = $this->createPerson($personData);
            $collection->add($person);
        }
        return $collection;
    }
    private function getFields(): string
    {
        return '
            `persons`.`id`,
            `persons`.`name`,
            `persons`.`fantasy_name`,
            `persons`.`birthdate`,
            `persons`.`foundation_date`,
            `persons`.`type`,
            `persons`.`status`
        ';
    }
    
    public function getTotalRows(): int
    {
        return (int) $this->pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
    }
    
    public function setStartingLine(int $lineInit): Storage
    {
        parent::setOffset($lineInit);
        return $this;
    }
    
    public function setTable(string $name): Database
    {
        $this->table = $name;
        return $this;
    }
    
    public function setTotalLines(int $total): Storage
    {
        parent::setLimit($total);
        return $this;
    }
}
