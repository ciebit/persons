<?php
namespace Ciebit\Persons;

use ArrayIterator;
use ArrayObject;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    private $persons; #:ArrayObject

    public function __construct()
    {
        $this->persons = new ArrayObject;
    }

    public function add(Person $person): self
    {
        $this->persons->append($person);
        return $this;
    }

    public function getArrayObject(): ArrayObject
    {
        return clone $this->persons;
    }

    public function getById(string $id): ?Person
    {
        foreach ($this->getIterator() as $person) {
            if ($person->getId() == $id) {
                return $person;
            }
        }
        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->persons->getIterator();
    }

    public function count(): int
    {
        return $this->persons->count();
    }
}
