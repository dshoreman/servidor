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
    }

    public function testToArray(): void
    {
        $database = new DatabaseData('name_only');

        $array = $database->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('name_only', $array['name']);
    }
}
