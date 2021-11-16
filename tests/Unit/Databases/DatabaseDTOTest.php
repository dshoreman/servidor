<?php

namespace Tests\Unit\Databases;

use Mockery;
use Servidor\Databases\DatabaseDTO;
use Servidor\Databases\TableDTO;
use Servidor\Http\Requests\Databases\NewDatabase;
use Tests\TestCase;

class DatabaseDTOTest extends TestCase
{
    public function testFromRequest(): void
    {
        $request = Mockery::mock(NewDatabase::class);
        $request->shouldReceive('validated')->andReturn(['database' => 'validated_db_name']);

        $database = DatabaseDTO::fromRequest($request);

        $this->assertInstanceOf(DatabaseDTO::class, $database);

        $this->assertObjectHasAttribute('name', $database);
        $this->assertObjectNotHasAttribute('database', $database);
        $this->assertEquals('validated_db_name', $database->name);

        $this->assertObjectHasAttribute('tableCount', $database);
        $this->assertNull($database->tableCount);
    }

    public function testToArray(): void
    {
        $database = new DatabaseDTO(name: 'name_only');

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

        $this->assertIsArray($array['tables']);
        $this->assertSame([], $array['tables']);
    }

    public function testWithTables(): void
    {
        $database = (new DatabaseDTO(name: 'collected_tables'))
            ->withTables([['foo'], ['bar']])
        ;

        $this->assertInstanceOf(DatabaseDTO::class, $database);
        $this->assertObjectHasAttribute('tables', $database);
        $this->assertArrayHasKey('tables', $database->toArray());

        $this->assertInstanceOf(TableCollection::class, $database->tables);
        $this->assertSame(array_map(
            static fn (string $table): array => (new TableDTO(name: $table))->toArray(),
            ['foo', 'bar'],
        ), $database->toArray()['tables']);
    }
}
