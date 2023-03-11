<?php

namespace Servidor\Databases;

use Spatie\LaravelData\Data;

class TableData extends Data
{
    public function __construct(
        public string $name,
        public string $collation = '',
        public string $engine = '',
        public int $rowCount = -1,
        public int $size = -1,
    ) {
    }

    /**
     * @param array{TABLE_NAME:string,TABLE_COLLATION:string,ENGINE:string,TABLE_ROWS:int,DATA_LENGTH:int} $result
     */
    public static function fromInfoSchema(array $result): self
    {
        return new self(
            name: $result['TABLE_NAME'],
            engine: $result['ENGINE'],
            collation: $result['TABLE_COLLATION'],
            rowCount: $result['TABLE_ROWS'],
            size: $result['DATA_LENGTH'],
        );
    }

    public static function fromString(string $tableName): self
    {
        return new self(
            name: $tableName,
        );
    }
}
