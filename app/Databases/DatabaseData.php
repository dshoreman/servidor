<?php

namespace Servidor\Databases;

use Illuminate\Contracts\Support\Arrayable;
use Servidor\Http\Requests\Databases\NewDatabase;

class DatabaseData implements Arrayable
{
    public string $name;

    public string $charset;

    public string $collation;

    public ?int $tableCount;

    public TableCollection $tables;

    public function __construct(
        string $name,
        ?TableCollection $tables = null,
        ?int $tableCount = null,
        string $charset = '',
        string $collation = ''
    ) {
        $this->name = $name;
        $this->tables = $tables ?? new TableCollection();
        $this->tableCount = $tableCount;
        $this->charset = $charset;
        $this->collation = $collation;
    }

    public static function fromRequest(NewDatabase $request): self
    {
        return new self($request->validated()['database']);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'tables' => $this->tables->toArray(),
            'tableCount' => $this->tableCount,
        ];
    }

    public function withTables(TableCollection $tables): self
    {
        return new self($this->name, $tables, $this->tableCount, $this->charset, $this->collation);
    }
}
