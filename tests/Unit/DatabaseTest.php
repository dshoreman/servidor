<?php

namespace Tests\Unit;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Servidor\Database;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function it_can_connect(): void
    {
        $db = new Database();

        $this->assertInstanceOf(MySqlSchemaManager::class, $db->dbal());
    }

    /** @test */
    public function it_can_list_databases(): void
    {
        $db = config('database.connections.mysql.database');
        $list = (new Database())->listDatabases();

        $this->assertIsArray($list);
        $this->assertContains($db, $list);
        $this->assertContains('information_schema', $list);
        $this->assertContains('performance_schema', $list);
        $this->assertContains('mysql', $list);
    }

    /** @test */
    public function it_can_create_a_database(): void
    {
        $db = new Database();
        $before = $db->listDatabases();

        $created = $db->create('testdb');
        $after = array_merge($before, ['testdb']);
        sort($after);

        $this->assertTrue($created);
        $this->assertSame($db->listDatabases(), $after);

        $db->dbal()->dropDatabase('testdb');
    }
}
