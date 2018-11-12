<?php
namespace Ciebit\Persons;

use DateTime;

class Legal extends Person
{
    private $fantasyName; #string
    private $foundationDate; #DateTime

    public function getFantasyName(): string
    {
        return $this->fantasyName;
    }

    public function getFoundationDate(): DateTime
    {
        return $this->foundationDate;
    }

    public function setFantasyName(string $name): self
    {
        $this->fantasyName = $name;
        return $this;
    }

    public function setFoundationDate(DateTime $date): self
    {
        $this->foundationDate = $date;
        return $this;
    }
}
