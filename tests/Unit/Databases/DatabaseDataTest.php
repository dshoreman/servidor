<?php

namespace Tests\Unit\Databases;

use Mockery;
use Servidor\Databases\DatabaseData;
use Servidor\Http\Requests\Databases\NewDatabase;
use Tests\TestCase;

class DatabaseDataTest extends TestCase
{
    public function testFromRequest(): void
    {
        $request = Mockery::mock(NewDatabase::class);
        $request->shouldReceive('validated')->andReturn(['database' => 'validated_db_name']);

        $database = DatabaseData::fromRequest($request);

        $this->assertInstanceOf(DatabaseData::class, $database);

        $this->assertObjectHasAttribute('name', $database);
        $this->assertObjectNotHasAttribute('database', $database);
        $this->assertEquals('validated_db_name', $database->name);

        $this->assertObjectHasAttribute('tableCount', $database);
        $this->assertNull($database->tableCount);
    }

    public function testToArray(): void
    {
        $database = new DatabaseData('name_only');

        $array = $database->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('name_only', $array['name']);

        $this->assertArrayHasKey('tableCount', $array);
        $this->assertNull($array['tableCount']);
    }

    public function testWithTableCount(): void
    {
        $database = new DatabaseData('counted_tables', 13);

        $this->assertInstanceOf(DatabaseData::class, $database);
        $this->assertObjectHasAttribute('tableCount', $database);
        $this->assertArrayHasKey('tableCount', $database->toArray());

        $this->assertIsInt($database->tableCount);
        $this->assertEquals(13, $database->tableCount);
    }
}
