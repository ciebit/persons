<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Status;
use DateTime;

use function array_merge;

class Legal extends Person
{
    public const TYPE = 'legal';

    /** @var string */
    private $fantasyName;

    /** @var DateTime|null */
    private $foundationDate;

    public function __construct(
        string $name, 
        string $slug,
        Status $status,
        string $description = '',
        string $imageId = '',
        string $id = '',
        string $fantasyName = '',
        DateTime $foundationDate = null
    ) {
        parent::__construct(
            $name, 
            $slug, 
            $status,
            $description,
            $imageId,
            $id
        );
        $this->fantasyName = $fantasyName;
        $this->foundationDate = $foundationDate;
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

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'fantasyName' => $this->getFantasyName(),
                'foundationDate' => $this->getFoundationDate() != null 
                    ? $this->getFoundationDate()->format('Y-m-d') 
                    : null,
                'type' => $this->getType()
            ]
        );
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
