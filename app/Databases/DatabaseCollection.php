<?php

namespace Servidor\Databases;

use Exception;
use Illuminate\Support\Collection;

class DatabaseCollection extends Collection
{
    /**
     * @var array<DatabaseData>
     */
    protected $items = [];

    /**
     * @return array<DatabaseData>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param string            $name
     * @param DatabaseData|null $default
     */
    public function get($name, $default = null): DatabaseData
    {
        if (parent::get($name)) {
            return $this->items[$name];
        }
        if ($default) {
            return $default;
        }

        throw new Exception("Database {$name} does not exist.");
    }

    /**
     * @param array<string> $databaseNames
     */
    public static function fromNames(array $databaseNames): self
    {
        $databases = array_map(
            static fn (string $name): DatabaseData => new DatabaseData($name),
            $databaseNames,
        );

        return (new self($databases))
            ->keyBy(static fn (DatabaseData $database): string => $database->name)
        ;
    }

    public function toArray(): array
    {
        return array_values(parent::toArray());
    }
}
