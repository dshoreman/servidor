<?php

namespace Servidor\Databases;

use Illuminate\Contracts\Support\Arrayable;
use Servidor\Http\Requests\Databases\NewDatabase;

class Database implements Arrayable
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromRequest(NewDatabase $request): self
    {
        return new self($request->validated()['database']);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
