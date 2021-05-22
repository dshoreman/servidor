<?php

namespace Tests\Unit\Databases;

use Servidor\Databases\TableData;
use Tests\TestCase;

class TableDataTest extends TestCase
{
    public function testToArray(): void
    {
        $table = new TableData('name_only');

        $array = $table->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('name_only', $array['name']);
    }
}
