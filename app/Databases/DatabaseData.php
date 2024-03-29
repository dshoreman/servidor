<?php

namespace Servidor\Databases;

use Servidor\Http\Requests\Databases\NewDatabase;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class DatabaseData extends Data
{
    public function __construct(
        public string $name,
        /** @var DataCollection<int, TableData>|Optional */
        public DataCollection|Optional $tables,
        public string $charset = '',
        public string $collation = '',
        public ?int $tableCount = null,
    ) {
    }

    public static function fromRequest(NewDatabase $request): self
    {
        return self::from($request->validated()['database']);
    }

    public static function fromString(string $name): self
    {
        return new self($name, new Optional());
    }

    /** @param array<mixed> $tables */
    public function withTables(array $tables): self
    {
        return self::from([
            'name' => $this->name,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'tableCount' => $this->tableCount,
            'tables' => $tables,
        ]);
    }
}
