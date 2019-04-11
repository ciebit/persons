<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Status;
use DateTime;

class Legal extends Person
{
    private const TYPE = 'legal';

    /** @var string */
    private $fantasyName;

    /** @var DateTime|null */
    private $foundationDate;

    public function __construct(string $name, string $slug, Status $status)
    {
        parent::__construct($name, $slug, $status);
        $this->fantasyName = '';
    }

    public function getFantasyName(): string
    {
        return $this->fantasyName;
    }

    public function getFoundationDate(): ?DateTime
    {
        return $this->foundationDate;
    }

    public function getType(): string
    {
        return self::TYPE;
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
