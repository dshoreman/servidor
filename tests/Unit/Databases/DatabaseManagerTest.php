<?php

namespace Tests\Unit\Databases;

use Servidor\Databases\DatabaseCollection;
use Servidor\Databases\DatabaseDTO;
use Servidor\Databases\DatabaseManager;
use Servidor\Databases\TableDTO;
use Tests\TestCase;

class DatabaseManagerTest extends TestCase
{
    private DatabaseManager $manager;

    /** @test */
    public function it_can_list_databases(): DatabaseManager
    {
        $manager = new DatabaseManager(config(), null, new FakeSchemaManager());

        $collection = $manager->databases();

        $this->assertInstanceOf(DatabaseCollection::class, $collection);
        $this->assertContainsOnlyInstancesOf(DatabaseDTO::class, $collection);

        return $manager;
    }

    /**
     * @depends it_can_list_databases
     */
    public function it_returns_all_results(DatabaseManager $manager): void
    {
        $appDb = config('database.connections.mysql.database');

        $this->assertIsArray($databases = $manager->databases()->toArray());

        $this->assertContains(['name' => $appDb], $databases);
        $this->assertContains(['name' => 'information_schema'], $databases);
        $this->assertContains(['name' => 'performance_schema'], $databases);
        $this->assertContains(['name' => 'mysql'], $databases);
    }

    /**
     * @test
     * @depends it_can_list_databases
     */
    public function it_can_list_databases_with_table_details(DatabaseManager $manager): void
    {
        $databases = $manager->detailedDatabases();

        $this->assertInstanceOf(DatabaseCollection::class, $databases);

        $database = $databases->get('servidor_testing');
        $this->assertObjectHasAttribute('tableCount', $database);
        $this->assertObjectHasAttribute('charset', $database);
        $this->assertObjectHasAttribute('collation', $database);
        $this->assertEquals(8, $database->tableCount);
        $this->assertEquals('utf8mb4', $database->charset);
        $this->assertContains($database->collation, ['utf8mb4_general_ci', 'utf8mb4_0900_ai_ci']);
    }

    /**
     * @test
     * @depends it_can_list_databases
     */
    public function it_can_create_a_database(DatabaseManager $manager): DatabaseManager
    {
        $data = new DatabaseDTO(name: 'testdb');
        $before = $manager->databases()->toArray();
        $expected = array_merge($before, [$data->toArray()]);

        $database = $manager->create($data);
        $actual = $manager->databases()->toArray();
        sort($expected);

        $this->assertInstanceOf(DatabaseDTO::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertCount(1 + \count($before), $actual);
        $this->assertSame($expected, $actual);

        return $manager;
    }

    /**
     * @test
     * @depends it_can_create_a_database
     */
    public function it_can_list_tables_of_a_database(DatabaseManager $manager): void
    {
        $database = $manager->databaseWithTables(new DatabaseDTO(name: 'servidor_testing'));

        $this->assertInstanceOf(TableCollection::class, $database->tables);

        $first = $database->tables->first();
        $this->assertInstanceOf(TableDTO::class, $first);
        $this->assertEquals('failed_jobs', $first->name);
    }

    /**
     * @test
     * @depends it_can_create_a_database
     */
    public function create_returns_database_when_it_already_exists(
        DatabaseManager $manager,
    ): void {
        $before = $manager->databases()->toArray();
        $database = $manager->create(new DatabaseDTO(name: 'testdb'));

        $this->assertInstanceOf(DatabaseDTO::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertCount(\count($before), $manager->databases());
        $this->assertSame($before, $manager->databases()->toArray());
    }
}
