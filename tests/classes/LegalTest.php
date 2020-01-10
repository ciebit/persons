<?php
declare(strict_types=1);

namespace Ciebit\Persons\Tests;

use Ciebit\Persons\Legal;
use Ciebit\Persons\Collection;
use Ciebit\Persons\Status;
use DateTime;
use PHPUnit\Framework\TestCase;

class LegalTest extends TestCase
{
    const DESCRIPTION = 'Description';
    const FANTASY_NAME = 'Brasil Modas';
    const FOUNDATION_DATE = '2018-01-06';
    const ID = '1';
    const IMAGE_ID = '11';
    const NAME = 'Francisco Peixoto Pereira LTDA';
    const SLUG = 'francisco-peixoto-pereira-ltda';
    const STATUS = Status::ACTIVE;

    public function getLegalPerson(): Legal
    {
        return new Legal(
            self::NAME,
            self::SLUG,
            new Status(self::STATUS),
            self::DESCRIPTION,
            self::IMAGE_ID,
            self::ID,
            self::FANTASY_NAME,
            new DateTime(self::FOUNDATION_DATE)
        );
    }

    public function testCreateFromManual(): void
    {
        $person = $this->getLegalPerson();

        $this->assertInstanceof(Legal::class, $person);
        $this->assertEquals(self::DESCRIPTION, $person->getDescription());
        $this->assertEquals(self::ID, $person->getId());
        $this->assertEquals(self::IMAGE_ID, $person->getImageId());
        $this->assertEquals(self::NAME, $person->getName());
        $this->assertEquals(self::SLUG, $person->getSlug());
        $this->assertEquals(self::FANTASY_NAME, $person->getFantasyName());
        $this->assertEquals(new DateTime(self::FOUNDATION_DATE), $person->getFoundationDate());
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
        $person = self::getLegalPerson();
        $json = json_encode($person);
        $this->assertJson($json);

        $data = json_decode($json);
        $this->assertEquals($person->getDescription(), $data->description);
        $this->assertEquals($person->getFantasyName(), $data->fantasyName);
        $this->assertEquals($person->getFoundationDate()->format('Y-m-d'), $data->foundationDate);
        $this->assertEquals($person->getId(), $data->id);
        $this->assertEquals($person->getImageId(), $data->imageId);
        $this->assertEquals($person->getName(), $data->name);
        $this->assertEquals($person->getSlug(), $data->slug);
        $this->assertEquals($person->getStatus()->getValue(), $data->status);
    }
}
