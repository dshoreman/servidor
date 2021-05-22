<?php

namespace Servidor\Databases;

use Exception;
use Illuminate\Support\Collection;

class TableCollection extends Collection
{
    /**
     * @var array<TableData>
     */
    protected $items = [];

    /**
     * @return array<TableData>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param string         $name
     * @param TableData|null $default
     */
    public function get($name, $default = null): TableData
    {
        if (parent::get($name)) {
            return $this->items[$name];
        }
        if ($default) {
            return $default;
        }

        throw new Exception("Table {$name} does not exist.");
    }
}
