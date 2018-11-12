<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Enum\Status;

abstract class Person
{
    private $id; #string
    private $name; #string
    private $status; #Status

    public function __construct(string $name, Status $status)
    {
        $this->name = $name;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }
}
