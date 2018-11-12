<?php
namespace Ciebit\Persons\Tests;

use Ciebit\Persons\Natural;
use Ciebit\Persons\Collection;
use PHPUnit\Framework\TestCase;
use Ciebit\Persons\Enum\Status;
use Ciebit\Persons\Enum\Gender;
use Ciebit\Persons\Enum\MaritalStatus;
use Ciebit\Persons\Enum\EducationalLevel;
use DateTime;

class NaturalPersonsTest extends TestCase
{
    const ID = '1';
    const NAME = 'Francisco Peixoto Pereira';
    const NICKNAME = 'Cotó';
    const BIRTH_DATE = '1993-02-18';
    const EDUCATIONAL_LEVEL = EducationalLevel::SUPERIOR;
    const GENDER = Gender::MALE;
    const MARITAL_STATUS = MaritalStatus::MARRIED;
    const STATUS = Status::ACTIVE;

    public function testCreateFromManual(): void
    {
        $person = new Natural(
            self::NAME,
            new Status(self::STATUS)
        );
        $person->setId(self::ID);
        $person->setBirthDate(new DateTime(self::BIRTH_DATE));
        $person->setEducationalLevel(new EducationalLevel(self::EDUCATIONAL_LEVEL));
        $person->setGender(new Gender(self::GENDER));
        $person->setMaritalStatus(new MaritalStatus(self::MARITAL_STATUS));
        $person->setNickname(self::NICKNAME);
        
        $this->assertInstanceof(Natural::class, $person);
        $this->assertEquals(self::ID, $person->getId());
        $this->assertEquals(self::NAME, $person->getName());
        $this->assertEquals(self::NICKNAME, $person->getNickname());
        $this->assertEquals(new DateTime(self::BIRTH_DATE), $person->getBirthDate());
        $this->assertEquals(new EducationalLevel(self::EDUCATIONAL_LEVEL), $person->getEducationalLevel());
        $this->assertEquals(new Gender(self::GENDER), $person->getGender());
        $this->assertEquals(new MaritalStatus(self::MARITAL_STATUS), $person->getMaritalStatus());
        $this->assertEquals(self::STATUS, $person->getStatus()->getValue());

        $newStatus = 5;
        $person->setStatus(new Status($newStatus));
        $this->assertEquals($newStatus, $person->getStatus()->getValue());

        $personsCollection = new Collection();
        $personsCollection->add($person);
        $person1 = $personsCollection->getById(self::ID);
        $this->assertInstanceof(Collection::class, $personsCollection);
        $this->assertEquals(1, $personsCollection->count());
        $this->assertEquals($person1, $person);
    }
}
