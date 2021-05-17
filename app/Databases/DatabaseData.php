<?php

namespace Servidor\Databases;

use Illuminate\Contracts\Support\Arrayable;
use Servidor\Http\Requests\Databases\NewDatabase;

class DatabaseData implements Arrayable
{
    public string $name;

    public ?int $tableCount;

    public function __construct(
        string $name,
        ?int $tableCount = null
    ) {
        $this->name = $name;
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
            'tableCount' => $this->tableCount,
        ];
    }

    public function withTableCount(int $count): self
    {
        return new self($this->name, $count);
    }
}
