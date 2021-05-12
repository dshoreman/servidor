<?php

namespace Servidor\Databases;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;
use Servidor\Http\Requests\Databases\NewDatabase;

class Database implements Arrayable
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromRequest(FormRequest $request): self
    {
        if ($request instanceof NewDatabase) {
            return new self($request->validated()['database']);
        }

        throw new Exception('Unhandled request type');
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
