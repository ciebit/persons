<?php
namespace Ciebit\Persons;

use Ciebit\Persons\Status;
use JsonSerializable;

abstract class Person implements JsonSerializable
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

    public function __construct(
        string $name, 
        string $slug, 
        Status $status,
        string $description, 
        string $imageId, 
        string $id = '' 
    ) {
        $this->id = $id;
        $this->imageId = $imageId;
        $this->description = $description;
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

    public function jsonSerialize(): array
    {
        return [
            'description' => $this->getDescription(),
            'id' => $this->getId(),
            'imageId' => $this->getImageId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'status' => $this->getStatus()
        ];
    }
}
