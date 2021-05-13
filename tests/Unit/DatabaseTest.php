<?php

namespace Tests\Unit;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Servidor\Databases\Database;
use Servidor\Databases\DatabaseCollection;
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
        $collection = (new DatabaseManager())->listDatabases();

        $this->assertInstanceOf(DatabaseCollection::class, $collection);
        $this->assertContainsOnlyInstancesOf(Database::class, $collection);

        $this->assertIsArray($array = $collection->toArray());
        $this->assertContains(['name' => $db], $array);
        $this->assertContains(['name' => 'information_schema'], $array);
        $this->assertContains(['name' => 'performance_schema'], $array);
        $this->assertContains(['name' => 'mysql'], $array);
    }

    /** @test */
    public function it_can_create_a_database(): DatabaseManager
    {
        $db = new DatabaseManager();

        if (in_array('testdb', $db->dbal()->listDatabases())) {
            $db->dbal()->dropDatabase('testdb');
        }

        $before = $db->dbal()->listDatabases();
        $database = $db->create(new Database('testdb'));

        $after = array_merge($before, ['testdb']);
        sort($after);

        $this->assertInstanceOf(Database::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertSame($after, $db->dbal()->listDatabases());

        return $db;
    }

    /**
     * @test
     * @depends it_can_create_a_database
     */
    public function create_returns_database_when_it_already_exists(
        DatabaseManager $db
    ): void {
        $before = $db->listDatabases()->toArray();
        $database = $db->create(new Database('testdb'));

        $this->assertInstanceOf(Database::class, $database);
        $this->assertEquals('testdb', $database->name);

        $after = $db->listDatabases()->toArray();
        $this->assertSame($before, $after);

        $db->dbal()->dropDatabase('testdb');
    }
}
