<?php

namespace Tests\Unit\Databases;

use Servidor\Databases\DatabaseData;
use Servidor\Databases\DatabaseManager;
use Servidor\Databases\TableData;
use Spatie\LaravelData\DataCollection;
use Tests\TestCase;

class DatabaseManagerTest extends TestCase
{
    private DatabaseManager $manager;

    /** @test */
    public function it_can_list_databases(): DatabaseManager
    {
        $this->manager = new DatabaseManager(config(), null, new FakeSchemaManager());

        $collection = $this->manager->databases();

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertContainsOnlyInstancesOf(DatabaseData::class, $collection);

        return $this->manager;
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
     *
     * @depends it_can_list_databases
     */
    public function it_can_list_databases_with_table_details(DatabaseManager $manager): void
    {
        $databases = $manager->detailedDatabases();

        $this->assertInstanceOf(DataCollection::class, $databases);

        $database = $databases->where('name', '=', 'servidor_testing')->first();
        $this->assertTrue(property_exists($database, 'tableCount'));
        $this->assertTrue(property_exists($database, 'charset'));
        $this->assertTrue(property_exists($database, 'collation'));
        $this->assertEquals(7, $database->tableCount);
        $this->assertEquals('utf8mb4', $database->charset);
        $this->assertContains($database->collation, ['utf8mb4_general_ci', 'utf8mb4_0900_ai_ci']);
    }

    /**
     * @test
     *
     * @depends it_can_list_databases
     */
    public function it_can_create_a_database(DatabaseManager $manager): DatabaseManager
    {
        $data = DatabaseData::from('testdb');
        $before = $manager->databases()->toArray();
        $expected = array_merge($before, [$data->toArray()]);

        $database = $manager->create($data);
        $actual = $manager->databases()->toArray();
        sort($expected);

        $this->assertInstanceOf(DatabaseData::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertCount(1 + \count($before), $actual);
        $this->assertSame($expected, $actual);

        return $manager;
    }

    /**
     * @test
     *
     * @depends it_can_create_a_database
     */
    public function it_can_list_tables_of_a_database(DatabaseManager $manager): void
    {
        $database = $manager->databaseWithTables(DatabaseData::from('servidor_testing'));

        $this->assertInstanceOf(DataCollection::class, $database->tables);

        $sorted = $database->tables->toArray();
        usort($sorted, static fn ($a, $b) => strcmp($a['name'], $b['name']));

        $this->assertInstanceOf(TableData::class, $database->tables->first());
        $this->assertEquals('failed_jobs', $sorted[0]['name']);
    }

    /**
     * @test
     *
     * @depends it_can_create_a_database
     */
    public function create_returns_database_when_it_already_exists(
        DatabaseManager $manager,
    ): void {
        $before = $manager->databases()->toArray();
        $database = $manager->create(DatabaseData::from('testdb'));

        $this->assertInstanceOf(DatabaseData::class, $database);
        $this->assertEquals('testdb', $database->name);
        $this->assertCount(\count($before), $manager->databases());
        $this->assertSame($before, $manager->databases()->toArray());
    }
}
