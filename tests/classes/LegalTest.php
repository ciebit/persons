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
    const ID = '1';
    const IMAGE_ID = '11';
    const NAME = 'Francisco Peixoto Pereira LTDA';
    const SLUG = 'francisco-peixoto-pereira-ltda';
    const FANTASY_NAME = 'Brasil Modas';
    const FOUNDATION_DATE = '2018-01-06';
    const STATUS = Status::ACTIVE;

    public function testCreateFromManual(): void
    {
        $person = new Legal(
            self::NAME,
            self::SLUG,
            new Status(self::STATUS)
        );
        $person->setId(self::ID);
        $person->setImageId(self::IMAGE_ID);
        $person->setFoundationDate(new DateTime(self::FOUNDATION_DATE));
        $person->setFantasyName(self::FANTASY_NAME);

        $this->assertInstanceof(Legal::class, $person);
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
}