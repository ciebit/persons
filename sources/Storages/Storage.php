<?php
namespace Ciebit\Persons\Storages;

use Ciebit\Persons\Collection;
use Ciebit\Persons\Person;
use Ciebit\Persons\Status;

interface Storage
{
    /** @var string */
    public const FIELD_BIRTH_DATE = 'birth_date';

    /** @var string */
    public const FIELD_DESCRIPTION = 'description';

    /** @var string */
    public const FIELD_EDUCATIONAL_LEVEL = 'educational_level';

    /** @var string */
    public const FIELD_FANTASY_NAME = 'nickname';

    /** @var string */
    public const FIELD_FOUNDATION_DATE = 'birth_date';

    /** @var string */
    public const FIELD_GENDER = 'gender';

    /** @var string */
    public const FIELD_ID = 'id';

    /** @var string */
    public const FIELD_IMAGE_ID = 'image_id';

    /** @var string */
    public const FIELD_MARITAL_STATUS = 'marital_status';

    /** @var string */
    public const FIELD_NAME = 'name';

    /** @var string */
    public const FIELD_NICKNAME = 'nickname';

    /** @var string */
    public const FIELD_SLUG = 'slug';

    /** @var string */
    public const FIELD_STATUS = 'status';

    /** @var string */
    public const FIELD_TYPE = 'type';

    public function addFilterById(string $operator, string ...$id): self;

    public function addFilterByName(string $operator, string ...$name): self;

    public function addFilterBySlug(string $operator, string ...$slug): self;

    public function addFilterByStatus(string $operator, Status ...$status): self;

    public function addFilterByType(string $operator, string ...$type): self;

    public function addOrderBy(string $field, string $direction): self;

    public function getTotalItemsOfLastFindWithoutLimitations(): int;

    public function findAll(): Collection;

    public function findOne(): ?Person;

    public function setLimit(int $limit): self;

    public function setOffset(int $offset): self;
}
