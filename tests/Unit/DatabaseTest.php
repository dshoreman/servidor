<?php

namespace Tests\Unit;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Servidor\Databases\Database;
use Servidor\Databases\DatabaseManager;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    public function it_can_connect(): void
    {
        $db = new DatabaseManager();

        $this->assertInstanceOf(MySqlSchemaManager::class, $db->dbal());
    }

    /** @test */
    public function it_can_list_databases(): void
    {
        $db = config('database.connections.mysql.database');
        $list = array_map(
            static fn (Database $database): array => $database->toArray(),
            (new DatabaseManager())->listDatabases(),
        );

        $this->assertIsArray($list);
        $this->assertContains(['name' => $db], $list);
        $this->assertContains(['name' => 'information_schema'], $list);
        $this->assertContains(['name' => 'performance_schema'], $list);
        $this->assertContains(['name' => 'mysql'], $list);
    }

    /** @test */
    public function it_can_create_a_database(): DatabaseManager
    {
        $db = new DatabaseManager();

        if (in_array('testdb', $db->dbal()->listDatabases())) {
            $db->dbal()->dropDatabase('testdb');
        }

        $before = $db->dbal()->listDatabases();
        $created = $db->create(new Database('testdb'));
        $after = array_merge($before, ['testdb']);

        sort($after);
        $this->assertTrue($created);
        $this->assertSame($after, $db->dbal()->listDatabases());

        return $db;
    }

    /**
     * @test
     * @depends it_can_create_a_database
     */
    public function it_returns_true_when_created_database_exists(
        DatabaseManager $db
    ): void {
        $before = array_map(static fn (Database $database): array => $database->toArray(), $db->listDatabases());

        $this->assertTrue($db->create(new Database('testdb')));

        $after = array_map(static fn (Database $database): array => $database->toArray(), $db->listDatabases());
        $this->assertSame($before, $after);

        $db->dbal()->dropDatabase('testdb');
    }
}
