<?php

namespace Servidor\Databases;

class TableData
{
    public string $collation;

    public string $engine;

    public string $name;

    public int $rowCount;

    public int $size;

    public function __construct(
        string $name,
        string $collation = '',
        string $engine = '',
        int $rowCount = -1,
        int $size = -1
    ) {
        $this->name = $name;

        $this->engine = $engine;
        $this->collation = $collation;

        $this->size = $size;
        $this->rowCount = $rowCount;
    }

    /**
     * @param array{TABLE_NAME:string,TABLE_COLLATION:string,ENGINE:string,TABLE_ROWS:int,DATA_LENGTH:int} $result
     */
    public static function fromInfoSchema(array $result): self
    {
        return new self(
            $result['TABLE_NAME'],
            $result['TABLE_COLLATION'],
            $result['ENGINE'],
            $result['TABLE_ROWS'],
            $result['DATA_LENGTH'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
