<?php

namespace Tests\Unit\Databases;

use PHPUnit\Framework\TestCase;
use Servidor\Databases\TableCollection;
use Servidor\Databases\TableDTO;

class TableCollectionTest extends TestCase
{
    public function testGetCanReturnTableByName(): void
    {
        $collection = (new TableCollection([
            new TableDTO('foo'),
            new TableDTO('bar'),
            new TableDTO('baz'),
        ]))->keyBy('name');

        $table = $collection->get('bar');

        $this->assertInstanceOf(TableDTO::class, $table);
        $this->assertEquals('bar', $table->name);
    }

    public function testGetReturnsDefaultWhenTableIsNotFound(): void
    {
        $collection = new TableCollection();
        $default = new TableDTO('NOMATCH');

        $this->assertInstanceOf(TableCollection::class, $collection);
        $this->assertEquals('NOMATCH', $collection->get('c', $default)->name);
    }

    public function testGetThrowsExceptionWhenNotFoundAndNoDefaultGiven(): void
    {
        $collection = new TableCollection();

        $this->expectExceptionMessage('cake does not exist');

        $collection->get('cake');
    }
}
