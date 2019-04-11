<?php
declare(strict_types=1);

namespace Ciebit\Persons\Tests;

use Ciebit\Persons\Characteristics\EducationalLevel;
use Ciebit\Persons\Characteristics\Gender;
use Ciebit\Persons\Characteristics\MaritalStatus;
use Ciebit\Persons\Collection;
use Ciebit\Persons\Natural;
use Ciebit\Persons\Status;
use DateTime;
use PHPUnit\Framework\TestCase;

class NaturalPersonsTest extends TestCase
{
    const ID = '1';
    const IMAGE_ID = '11';
    const NAME = 'Francisco Peixoto Pereira';
    const SLUG = 'francisco-peixoto-pereira';
    const NICKNAME = 'Cotó';
    const BIRTH_DATE = '1993-02-18';
    const EDUCATIONAL_LEVEL = EducationalLevel::HIGHER_EDUCATION;
    const GENDER = Gender::MALE;
    const MARITAL_STATUS = MaritalStatus::MARRIED;
    const STATUS = Status::ACTIVE;

    public function testCreateFromManual(): void
    {
        $person = new Natural(
            self::NAME,
            self::SLUG,
            new Status(self::STATUS)
        );
        $person->setId(self::ID);
        $person->setImageId(self::IMAGE_ID);
        $person->setBirthDate(new DateTime(self::BIRTH_DATE));
        $person->setEducationalLevel(new EducationalLevel(self::EDUCATIONAL_LEVEL));
        $person->setGender(new Gender(self::GENDER));
        $person->setMaritalStatus(new MaritalStatus(self::MARITAL_STATUS));
        $person->setNickname(self::NICKNAME);

        $this->assertInstanceof(Natural::class, $person);
        $this->assertEquals(self::ID, $person->getId());
        $this->assertEquals(self::IMAGE_ID, $person->getImageId());
        $this->assertEquals(self::NAME, $person->getName());
        $this->assertEquals(self::SLUG, $person->getSlug());
        $this->assertEquals(self::NICKNAME, $person->getNickname());
        $this->assertEquals(new DateTime(self::BIRTH_DATE), $person->getBirthDate());
        $this->assertEquals(new EducationalLevel(self::EDUCATIONAL_LEVEL), $person->getEducationalLevel());
        $this->assertEquals(new Gender(self::GENDER), $person->getGender());
        $this->assertEquals(new MaritalStatus(self::MARITAL_STATUS), $person->getMaritalStatus());
        $this->assertEquals(self::STATUS, $person->getStatus()->getValue());

        $personsCollection = new Collection();
        $personsCollection->add($person);
        $person1 = $personsCollection->getById(self::ID);
        $this->assertInstanceof(Collection::class, $personsCollection);
        $this->assertEquals(1, $personsCollection->count());
        $this->assertEquals($person1, $person);
    }
}
