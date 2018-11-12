<?php
namespace Ciebit\Persons\Tests\Storages;

use Ciebit\Persons\Collection;
use Ciebit\Persons\Enum\Status;
use Ciebit\Persons\Person;
use Ciebit\Persons\Legal;
use Ciebit\Persons\Natural;
use Ciebit\Persons\Storages\Database\Sql as DatabaseSql;
use Ciebit\Persons\Tests\Connection;
use DateTime;

class DatabaseSqlTest extends Connection
{
    public function getDatabase(): DatabaseSql
    {
        $pdo = $this->getPdo();
        return new DatabaseSql($pdo);
    }

    public function testGet(): void
    {
        $database = $this->getDatabase();
        $person = $database->get();
        $this->assertInstanceOf(Legal::class, $person);
    }
    
    public function testGetAll(): void
    {
        $database = $this->getDatabase();
        $persons = $database->getAll();
        $this->assertInstanceOf(Collection::class, $persons);
        $this->assertCount(6, $persons->getIterator());
    }
    
    public function testGetAllBugUniqueValue(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByIds('=', 1, 2);
        $persons = $database->getAll();
        $this->assertInstanceOf(Collection::class, $persons);
    }
    
    public function testGetAllFilterById(): void
    {
        $id = 3;
        $database = $this->getDatabase();
        $database->addFilterById($id+0);
        $persons = $database->getAll();
        $this->assertCount(1, $persons->getIterator());
        $this->assertEquals($id, $persons->getArrayObject()->offsetGet(0)->getId());
    }
    
    public function testGetAllFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus(Status::ACTIVE());
        $persons = $database->getAll();
        $this->assertCount(2, $persons->getIterator());
        $this->assertEquals(Status::ACTIVE(), $persons->getArrayObject()->offsetGet(0)->getStatus());
    }
    
    public function testGetFilterById(): void
    {
        $id = 2;
        $database = $this->getDatabase();
        $database->addFilterById($id+0);
        $person = $database->get();
        $this->assertEquals($id, $person->getId());
    }
    
    public function testGetFilterByIds(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByIds('=', 2, 3);
        $person = $database->getAll();
        $this->assertCount(2, $person);
        $this->assertEquals(2, $person->getById(2)->getId());
        $this->assertEquals(3, $person->getById(3)->getId());
    }
    
    public function testGetFilterByName(): void
    {
        $name = 'João Silva Diógenes';
        $database = $this->getDatabase();
        $database->addFilterByName($name);
        $person = $database->get();
        $this->assertEquals($name, $person->getName());
    }
    
    public function testGetFilterByStatus(): void
    {
        $database = $this->getDatabase();
        $database->addFilterByStatus(Status::ACTIVE());
        $person = $database->get();
        $this->assertEquals(Status::ACTIVE(), $person->getStatus());
    }
    
    public function testGetAllByOrderDesc(): void
    {
        $database = $this->getDatabase();
        $database->orderBy('id', 'DESC');
        $person = $database->get();
        $this->assertEquals(6, (int) $person->getId());
    }
    
    public function testStore(): void
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
        $this->assertEquals($person, $database->get());

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
        $this->assertEquals($person, $database->get());
    }
    
    public function testUpdate(): void
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
        $this->assertEquals($newName, $database->get()->getName());

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
        $this->assertEquals($newName, $database->get()->getName());
    }
    
    public function testSave(): void
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
        $this->assertEquals($newName, $database->get()->getName());
        $newId = '123456';
        $person->setId($newId);
        $database->addFilterById($newId);
        $database->save($person);
        $this->assertEquals($newName, $database->get()->getName());
    }
    
    public function testDestroy(): void
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
        $this->assertEquals($id, $database->get()->getId());
        $database->destroy($person);
        $this->assertEquals(null, $database->get());
    }
}
