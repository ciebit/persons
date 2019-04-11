<?php
declare(strict_types=1);

namespace Ciebit\Persons\Tests\Storages\Database;

use Ciebit\Persons\Collection;
use Ciebit\Persons\Status;
use Ciebit\Persons\Person;
use Ciebit\Persons\Legal;
use Ciebit\Persons\Natural;
use Ciebit\Persons\Storages\Database\Sql as DatabaseSql;
use Ciebit\Persons\Storages\Storage;
use Ciebit\Persons\Tests\Connection;
use DateTime;

class SqlTest extends Connection
{
    public function getDatabase(): DatabaseSql
    {
        $pdo = $this->getPdo();
        return new DatabaseSql($pdo);
    }

    public function testFindOne(): void
    {
        $database = $this->getDatabase();
        $person = $database->findOne();
        $this->assertInstanceOf(Legal::class, $person);
    }

    public function testFindAll(): void
    {
        $database = $this->getDatabase();
        $persons = $database->findAll();
        $this->assertInstanceOf(Collection::class, $persons);
        $this->assertCount(6, $persons);
    }

    public function testFindAllBugUniqueValue(): void
    {
        $database = $this->getDatabase();
        $database->addFilterById('=', '1', '2');
        $persons = $database->findAll();
        $this->assertInstanceOf(Collection::class, $persons);
    }

    public function testFindAllFilterById(): void
    {
        $id = '3';
        $database = $this->getDatabase();
        $database->addFilterById('=', $id.'');
        $persons = $database->findAll();
        $this->assertCount(1, $persons->getIterator());
        $this->assertEquals($id, $persons->getArrayObject()->offsetGet(0)->getId());
    }

    public function testFindAllFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::ACTIVE());
        $persons = $database->findAll();
        $this->assertCount(2, $persons->getIterator());
        $this->assertEquals(Status::ACTIVE(), $persons->getArrayObject()->offsetGet(0)->getStatus());
    }

    public function testGetFilterById(): void
    {
        $id = '2';
        $database = $this->getDatabase();
        $database->addFilterById('=', $id.'');
        $person = $database->findOne();
        $this->assertEquals($id, $person->getId());
    }

    public function testGetFilterByIds(): void
    {
        $database = $this->getDatabase();
        $database->addFilterById('=', '2', '3');
        $person = $database->findAll();
        $this->assertCount(2, $person);
        $this->assertEquals('2', $person->getById('2')->getId());
        $this->assertEquals('3', $person->getById('3')->getId());
    }

    public function testGetFilterByName(): void
    {
        $name = 'João Silva Diógenes';
        $database = $this->getDatabase();
        $database->addFilterByName('=', $name);
        $person = $database->findOne();
        $this->assertEquals($name, $person->getName());
    }

    public function testGetFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus('=', Status::ACTIVE());
        $person = $database->findOne();
        $this->assertEquals(Status::ACTIVE(), $person->getStatus());
    }

    public function testFindAllByOrderDesc(): void
    {
        $database = $this->getDatabase();
        $database->addOrderBy(Storage::FIELD_ID, 'DESC');
        $person = $database->findOne();
        $this->assertEquals('6', $person->getId());
    }

    public function Store(): void
    {
        $id = '7';
        $person = (new Natural(
            'Peter Parker',
            new Status(Status::ACTIVE)
        ))
        ->setId($id)
        ->setBirthDate(new DateTime('1960-05-25'));
        $database = $this->getDatabase();
        $database->store($person);
        $database->addFilterById($id);
        $this->assertEquals($person, $database->findOne());

        $newId = '8';
        $person = (new Legal(
            'Mary Jane',
            new Status(Status::ACTIVE)
        ))
        ->setId($newId)
        ->setFantasyName('Spider\'s Girl')
        ->setFoundationDate(new DateTime('1960-05-02'));
        $database = $this->getDatabase();
        $database->store($person);
        $database->addFilterById($newId);
        $this->assertEquals($person, $database->findOne());
    }

    public function Update(): void
    {
        $id = '3';
        $newName = 'Peter Véi';
        $person = (new Natural(
            $newName,
            new Status(Status::INACTIVE)
        ))
        ->setId($id)
        ->setBirthDate(new DateTime('1960-05-25'));
        $database = $this->getDatabase();
        $database->update($person);
        $database->addFilterById($id);
        $this->assertEquals($newName, $database->findOne()->getName());

        $newId = '1';
        $newName = 'Nome Alterado';
        $person = (new Legal(
            $newName,
            new Status(Status::ACTIVE)
        ))
        ->setId($newId)
        ->setFantasyName('Nome Fantasia')
        ->setFoundationDate(new DateTime('1888-05-25'));
        $database = $this->getDatabase();
        $database->update($person);
        $database->addFilterById($newId);
        $this->assertEquals($newName, $database->findOne()->getName());
    }

    public function Save(): void
    {
        $id = '1';
        $newName = 'Maike Alves Negreiros';
        $person = (new Natural(
            $newName,
            new Status(Status::INACTIVE)
        ))
        ->setId($id)
        ->setBirthDate(new DateTime('1993-02-18'));
        $database = $this->getDatabase();
        $database->addFilterById($id);
        $database->save($person);
        $this->assertEquals($newName, $database->findOne()->getName());
        $newId = '123456';
        $person->setId($newId);
        $database->addFilterById($newId);
        $database->save($person);
        $this->assertEquals($newName, $database->findOne()->getName());
    }

    public function Destroy(): void
    {
        $id = '9';
        $person = (new Natural(
            'Clark Kent',
            new Status(Status::ACTIVE)
        ))
        ->setId($id)
        ->setBirthDate(new DateTime('1960-05-25'));
        $database = $this->getDatabase();
        $database->store($person);
        $database->addFilterById($id);
        $this->assertEquals($id, $database->findOne()->getId());
        $database->destroy($person);
        $this->assertEquals(null, $database->findOne());
    }
}
