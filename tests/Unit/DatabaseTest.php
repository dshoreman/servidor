<?php

namespace Tests\Unit;

use Doctrine\DBAL\Schema\MysqlSchemaManager;
use Servidor\Database;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function it_can_connect()
    {
        $db = new Database();

        $this->assertInstanceOf(MysqlSchemaManager::class, $db->dbal());
    }

    /** @test */
    public function it_can_list_databases()
    {
        $list = (new Database())->listDatabases();

        $this->assertIsArray($list);
        $this->assertContains('servidor', $list);
    }
}
