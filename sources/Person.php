<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Status;

abstract class Person
{
    /** @var string */
    private $description;

    /** @var string */
    private $id;

    /** @var string */
    private $imageId;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var Status */
    private $status;

    public function __construct(string $name, string $slug, Status $status)
    {
        $this->id = '';
        $this->imageId = '';
        $this->description = '';
        $this->name = $name;
        $this->slug = $slug;
        $this->status = $status;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    abstract public function getType(): string;

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setImageId(string $id): self
    {
        $this->imageId = $id;
        return $this;
    }
}
