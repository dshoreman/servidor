<?php

namespace Tests\Unit\Databases;

use PHPUnit\Framework\TestCase;
use Servidor\Databases\TableCollection;
use Servidor\Databases\TableData;

class TableCollectionTest extends TestCase
{
    public function testGetCanReturnTableByName(): void
    {
        $collection = (new TableCollection([
            new TableData('foo'),
            new TableData('bar'),
            new TableData('baz'),
        ]))->keyBy('name');

        $table = $collection->get('bar');

        $this->assertInstanceOf(TableData::class, $table);
        $this->assertEquals('bar', $table->name);
    }

    public function testGetReturnsDefaultWhenTableIsNotFound(): void
    {
        $collection = new TableCollection();
        $default = new TableData('NOMATCH');

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
