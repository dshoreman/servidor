<?php

namespace Servidor\Databases;

class TableData
{
    public string $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
