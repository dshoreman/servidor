<?php

namespace Tests\Unit;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Servidor\Databases\Database;
use Servidor\Databases\DatabaseCollection;
use Servidor\Databases\DatabaseManager;
use Tests\TestCase;
use Tests\Unit\Databases\DummyConnection;

class DatabaseManagerTest extends TestCase
{
    private DatabaseManager $manager;

    /** @test */
    public function it_can_list_databases(): void
    {
        $appDb = config('database.connections.mysql.database');
        $collection = (new DatabaseManager(config()))->databases();

        $this->assertInstanceOf(DatabaseCollection::class, $collection);
        $this->assertContainsOnlyInstancesOf(Database::class, $collection);

        $this->assertIsArray($array = $collection->toArray());
        $this->assertContains(['name' => $appDb], $array);
        $this->assertContains(['name' => 'information_schema'], $array);
        $this->assertContains(['name' => 'performance_schema'], $array);
        $this->assertContains(['name' => 'mysql'], $array);
    }

    /** @test */
    public function it_can_create_a_database(): DatabaseManager
    {
        $manager = new DatabaseManager(config(), new DummyConnection());

        $data = new Database('testdb');
        $before = $manager->databases()->toArray();
        $expected = array_merge($before, [$data->toArray()]);

        $database = $manager->create($data);
        $actual = $manager->databases()->toArray();
        sort($expected);

        $this->assertInstanceOf(Database::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertCount(1 + count($before), $actual);
        $this->assertSame($expected, $actual);

        return $manager;
    }

    /**
     * @test
     * @depends it_can_create_a_database
     */
    public function create_returns_database_when_it_already_exists(
        DatabaseManager $manager
    ): void {
        $before = $manager->databases()->toArray();
        $database = $manager->create(new Database('testdb'));

        $this->assertInstanceOf(Database::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertCount(count($before), $manager->databases());
        $this->assertSame($before, $manager->databases()->toArray());
    }
}
