<?php

namespace Tests\Unit\Databases;

use Servidor\Databases\TableDTO;
use Tests\TestCase;

class TableDTOTest extends TestCase
{
    public function testFromInfoSchema(): void
    {
        $result = [
            'TABLE_NAME' => 'foo',
            'TABLE_COLLATION' => 'utf8_general_ci',
            'ENGINE' => 'Innodb',
            'TABLE_ROWS' => 0,
            'DATA_LENGTH' => 16 * 1024,
        ];
        $table = TableDTO::fromInfoSchema($result);

        $this->assertInstanceOf(TableDTO::class, $table);

        $this->assertObjectHasAttribute('name', $table);
        $this->assertEquals($result['TABLE_NAME'], $table->name);
    }

    public function testToArray(): void
    {
        $table = new TableDTO('name_only');

        $array = $table->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('name_only', $array['name']);
    }
}
