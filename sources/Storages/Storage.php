<?php
namespace Ciebit\Persons\Storages;

use Ciebit\Persons\Collection;
use Ciebit\Persons\Person;
use Ciebit\Persons\Enum\Status;

interface Storage
{
    public function addFilterById(int $id, string $operator = '='): self;
    
    public function addFilterByName(string $username, string $operator = '='): self;
    
    public function addFilterByStatus(Status $status, string $operator = '='): self;
    
    public function get(): ?Person;
    
    public function getAll(): Collection;
    
    public function store(Person $user): self;
    
    public function update(Person $user): self;
    
    public function save(Person $user): self;
    
    public function destroy(Person $user): self;
    
    public function setStartingLine(int $lineInit): self;
    
    public function setTotalLines(int $total): self;
}
