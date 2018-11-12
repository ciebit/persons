<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Enum\EducationalLevel;
use Ciebit\Persons\Enum\Gender;
use Ciebit\Persons\Enum\MaritalStatus;
use DateTime;

class Natural extends Person
{
    private $birthDate; #DateTime
    private $educationalLevel; #EducationalLevel
    private $gender; #Gender
    private $maritalStatus; #MaritalStatus
    private $nickname; #string
    private const TYPE = 'natural';

    public function getBirthDate(): DateTime
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
