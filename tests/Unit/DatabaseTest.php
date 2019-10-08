<?php

namespace Tests\Unit;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Servidor\Database;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function it_can_connect()
    {
        $db = new Database();

        $this->assertInstanceOf(MySqlSchemaManager::class, $db->dbal());
    }

    /** @test */
    public function it_can_list_databases()
    {
        $db = config('database.connections.mysql.database');
        $list = (new Database())->listDatabases();

        $this->assertIsArray($list);
        $this->assertContains($db, $list);
        $this->assertContains('information_schema', $list);
        $this->assertContains('performance_schema', $list);
        $this->assertContains('mysql', $list);
    }
}
