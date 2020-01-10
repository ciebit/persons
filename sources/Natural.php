<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Characteristics\EducationalLevel;
use Ciebit\Persons\Characteristics\Gender;
use Ciebit\Persons\Characteristics\MaritalStatus;
use Ciebit\Persons\Status;
use DateTime;

class Natural extends Person
{
    /** @var string */
    public const TYPE = 'natural';

    /** @var DateTime|null */
    private $birthDate;

    /** @var EducationalLevel */
    private $educationalLevel;

    /** @var Gender */
    private $gender;

    /** @var MaritalStatus */
    private $maritalStatus;

    /** @var string */
    private $nickname;

    public function __construct(
        string $name, 
        string $slug, 
        Status $status,
        string $description = '',
        string $imageId = '',
        string $id = '',
        DateTime $birthDate = null,
        EducationalLevel $educationalLevel,
        Gender $gender,
        MaritalStatus $maritalStatus,
        string $nickname
    ) {
        parent::__construct(
            $name,
            $slug,
            $status,
            $description,
            $imageId,
            $id
        );
        $this->birthDate = $birthDate;
        $this->educationalLevel = $educationalLevel;
        $this->gender = $gender;
        $this->maritalStatus = $maritalStatus;
        $this->nickname = $nickname;
    }

    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    public function getEducationalLevel(): EducationalLevel
    {
        return $this->educationalLevel;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function getMaritalStatus(): MaritalStatus
    {
        return $this->maritalStatus;
    }

    public function getNickname(): string
    {
        return $this->nickname;
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
                'birthDate' => $this->getBirthDate() != null 
                    ? $this->getBirthDate()->format('Y-m-d')
                    : null,
                'educationalLevel' => $this->getEducationalLevel(),
                'gender' => $this->getGender(),
                'maritalStatus' => $this->getMaritalStatus(),
                'nickname' => $this->getNickname(),
                'type' => $this->getType(),
            ]
        );
    }
}
