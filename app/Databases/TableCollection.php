<?php

namespace Servidor\Databases;

use Exception;
use Illuminate\Support\Collection;

class TableCollection extends Collection
{
    /**
     * @var array<TableDTO>
     */
    protected $items = [];

    /**
     * @return array<TableDTO>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param string        $name
     * @param TableDTO|null $default
     */
    public function get($name, $default = null): TableDTO
    {
        if (parent::get($name)) {
            return $this->items[$name];
        }
        if ($default) {
            return $default;
        }

        throw new Exception("Table {$name} does not exist.");
    }

    public function toArray(): array
    {
        return array_values(parent::toArray());
    }
}
