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
    private const TYPE = 'natural';

    /** @var DateTime|null */
    private $birthDate;

    /** @var EducationLevel */
    private $educationalLevel;

    /** @var Gender */
    private $gender;

    /** @var MaritalStatus */
    private $maritalStatus;

    /** @var string */
    private $nickname;

    public function __construct(string $name, string $slug, Status $status)
    {
        parent::__construct($name, $slug, $status);
        $this->educationalLevel = EducationalLevel::UNDEFINED;
        $this->gender = Gender::UNDEFINED;
        $this->maritalStatus = MaritalStatus::UNDEFINED;
        $this->nickname = '';
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

    public function setBirthDate(DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function setEducationalLevel(EducationalLevel $educationalLevel): self
    {
        $this->educationalLevel = $educationalLevel;
        return $this;
    }

    public function setGender(Gender $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function setMaritalStatus(MaritalStatus $maritalStatus): self
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;
        return $this;
    }
}
