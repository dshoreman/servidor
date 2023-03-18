<?php

namespace Tests\Unit\Databases;

use Mockery;
use Servidor\Databases\DatabaseData;
use Servidor\Databases\TableData;
use Servidor\Http\Requests\Databases\NewDatabase;
use Spatie\LaravelData\DataCollection;
use Tests\TestCase;

class DatabaseDataTest extends TestCase
{
    public function testFromRequest(): void
    {
        $request = Mockery::mock(NewDatabase::class);
        $request->shouldReceive('validated')->andReturn(['database' => 'validated_db_name']);

        $database = DatabaseData::fromRequest($request);

        $this->assertInstanceOf(DatabaseData::class, $database);

        $this->assertTrue(property_exists($database, 'name'));
        $this->assertFalse(property_exists($database, 'database'));
        $this->assertEquals('validated_db_name', $database->name);

        $this->assertTrue(property_exists($database, 'tableCount'));
        $this->assertNull($database->toArray()['tableCount']);
    }

    public function testToArray(): void
    {
        $database = DatabaseData::from('name_only');

        $array = $database->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('name_only', $array['name']);

        $this->assertArrayHasKey('charset', $array);
        $this->assertEquals('', $array['charset']);

        $this->assertArrayHasKey('collation', $array);
        $this->assertEquals('', $array['collation']);

        $this->assertArrayHasKey('tableCount', $array);
        $this->assertNull($array['tableCount']);

        $this->assertArrayNotHasKey('tables', $array);
    }

    public function testWithTables(): void
    {
        $database = DatabaseData::from('collected_tables')->withTables(['foo', 'bar']);

        $this->assertInstanceOf(DatabaseData::class, $database);
        $this->assertTrue(property_exists($database, 'tables'));
        $this->assertArrayHasKey('tables', $database->toArray());
        $this->assertSame($database->toArray()['tables'], $database->tables->toArray());

        $this->assertInstanceOf(DataCollection::class, $database->tables);
        $this->assertSame(array_map(
            static fn (string $table): array => (new TableData(name: $table))->toArray(),
            ['foo', 'bar'],
        ), $database->tables->toArray());
    }
}
