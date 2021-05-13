<?php

namespace Servidor\Databases;

use Illuminate\Support\Collection;

class DatabaseCollection extends Collection
{
    /**
     * @var array<Database>
     */
    protected $items = [];

    /**
     * @return array<Database>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param array<string> $databaseNames
     */
    public static function fromNames(array $databaseNames): self
    {
        $databases = array_map(
            static fn (string $name): Database => new Database($name),
            $databaseNames,
        );

        return new self($databases);
    }
}
