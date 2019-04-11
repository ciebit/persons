<?php
namespace Ciebit\Persons\Storages\Database;

use Ciebit\Persons\Person;
use Ciebit\Persons\Collection;
use Ciebit\Persons\Storages\Storage;

interface Database extends Storage
{
    public function setTable(string $name): self;
}
