<?php

namespace Tests\Firevel\FirestoreCacheDriver\Fixtures;

class TestPerson
{
    public $name;

    protected $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}
