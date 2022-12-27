<?php

namespace Servidor\Databases;

use Spatie\DataTransferObject\Caster;

class TableCollectionCaster implements Caster
{
    public function cast(mixed $value): TableCollection
    {
        if ($value instanceof TableCollection) {
            return $value;
        }
        \assert(\is_array($value));

        return new TableCollection(array_map(
            /** @param array{name: string}|TableDTO $data */
            static fn (TableDTO|array $data) => $data instanceof TableDTO
                ? $data : new TableDTO(...$data),
            $value,
        ));
    }
}
