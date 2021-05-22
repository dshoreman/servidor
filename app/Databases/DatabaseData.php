<?php

namespace Servidor\Databases;

use Illuminate\Contracts\Support\Arrayable;
use Servidor\Http\Requests\Databases\NewDatabase;

class DatabaseData implements Arrayable
{
    public string $name;

    public ?int $tableCount;

    public TableCollection $tables;

    public function __construct(
        string $name,
        ?TableCollection $tables = null,
        ?int $tableCount = null
    ) {
        $this->name = $name;
        $this->tables = $tables ?? new TableCollection();
        $this->tableCount = $tableCount;
    }

    public static function fromRequest(NewDatabase $request): self
    {
        return new self($request->validated()['database']);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'tables' => $this->tables->toArray(),
            'tableCount' => $this->tableCount,
        ];
    }

    public function withTableCount(int $count): self
    {
        return new self($this->name, $this->tables, $count);
    }

    public function withTables(TableCollection $tables): self
    {
        return new self($this->name, $tables, $this->tableCount);
    }
}
