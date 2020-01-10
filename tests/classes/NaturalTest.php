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
    const BIRTH_DATE = '1993-02-18';
    const DESCRIPTION = 'Description';
    const EDUCATIONAL_LEVEL = EducationalLevel::HIGHER_EDUCATION;
    const GENDER = Gender::MALE;
    const ID = '1';
    const IMAGE_ID = '11';
    const MARITAL_STATUS = MaritalStatus::MARRIED;
    const NAME = 'Francisco Peixoto Pereira';
    const NICKNAME = 'Cotó';
    const SLUG = 'francisco-peixoto-pereira';
    const STATUS = Status::ACTIVE;

    public function getNaturalPerson(): Natural
    {
        return new Natural(
            self::NAME,
            self::SLUG,
            new Status(self::STATUS),
            self::DESCRIPTION,
            self::IMAGE_ID,
            self::ID,
            new DateTime(self::BIRTH_DATE),
            new EducationalLevel(self::EDUCATIONAL_LEVEL),
            new Gender(self::GENDER),
            new MaritalStatus(self::MARITAL_STATUS),
            self::NICKNAME
        );
    }

    public function testCreateFromManual(): void
    {
        $person = $this->getNaturalPerson();

        $this->assertInstanceof(Natural::class, $person);
        $this->assertEquals(self::DESCRIPTION, $person->getDescription());
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

    public function testJsonSerialize(): void
    {
        $person = self::getNaturalPerson();
        $json = json_encode($person);
        $this->assertJson($json);

        $data = json_decode($json);
        $this->assertEquals($person->getBirthDate()->format('Y-m-d'), $data->birthDate);
        $this->assertEquals($person->getDescription(), $data->description);
        $this->assertEquals($person->getEducationalLevel()->getValue(), $data->educationalLevel);
        $this->assertEquals($person->getGender()->getValue(), $data->gender);
        $this->assertEquals($person->getId(), $data->id);
        $this->assertEquals($person->getImageId(), $data->imageId);
        $this->assertEquals($person->getMaritalStatus()->getValue(), $data->maritalStatus);
        $this->assertEquals($person->getName(), $data->name);
        $this->assertEquals($person->getNickname(), $data->nickname);
        $this->assertEquals($person->getSlug(), $data->slug);
        $this->assertEquals($person->getStatus()->getValue(), $data->status);
    }
}
