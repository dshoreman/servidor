<?php

namespace Servidor\Databases;

use Illuminate\Contracts\Support\Arrayable;
use Servidor\Http\Requests\Databases\NewDatabase;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class DatabaseDTO extends DataTransferObject implements Arrayable
{
    public string $name = '';

    public string $charset = '';

    public string $collation = '';

    public ?int $tableCount = null;

    /** @var TableDTO[] */
    #[CastWith(ArrayCaster::class, itemType: TableDTO::class)]
    public array $tables = [];

    public static function fromRequest(NewDatabase $request): self
    {
        return new self(name: $request->validated()['database']);
    }

    public function withTables(array $tables): self
    {
        return $this->clone(tables: $tables);
    }
}
