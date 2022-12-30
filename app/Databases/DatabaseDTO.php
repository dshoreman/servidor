<?php

namespace Servidor\Databases;

use Illuminate\Contracts\Support\Arrayable;
use Servidor\Http\Requests\Databases\NewDatabase;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class DatabaseDTO extends DataTransferObject implements Arrayable
{
    public string $name = '';

    public string $charset = '';

    public string $collation = '';

    public ?int $tableCount = null;

    #[CastWith(TableCollectionCaster::class)]
    public TableCollection $tables;

    public function __construct(mixed ...$args)
    {
        $this->tables = new TableCollection();

        if (!isset($args['tables'])) {
            $args['tables'] = $this->tables;
        }

        parent::__construct($args);
    }

    public static function fromRequest(NewDatabase $request): self
    {
        return new self(name: $request->validated()['database']);
    }

    public function withTables(array $tables): self
    {
        return $this->clone(tables: $tables);
    }

    protected function parseArray(array $array): array
    {
        if (($array['tables'] ?? null) && $array['tables'] instanceof TableCollection) {
            $array['tables'] = $array['tables']->toArray();
        }

        return parent::parseArray($array);
    }
}
