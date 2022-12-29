<?php

namespace Tests\Feature\Api\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Mockery\MockInterface;
use Servidor\Databases\DatabaseManager;
use Tests\RequiresAuth;
use Tests\TestCase;

class ShowDatabaseTest extends TestCase
{
    use RequiresAuth;

    protected string $endpoint = '/api/databases/{id}';

    public function testItCanListTables(): void
    {
        $this->mock(AbstractSchemaManager::class, static function (MockInterface $manager): void {
            $manager->shouldNotHaveBeenCalled();
        });
        $this->mock(Connection::class, static function (MockInterface $conn): void {
            $data = ['ENGINE' => 'InnoDB', 'TABLE_COLLATION' => 'utf8mb4_unicode_ci', 'DATA_LENGTH' => 9001];

            $conn->shouldReceive('fetchAllAssociative')->once()
                ->with(DatabaseManager::tablesSql(), ['db' => 'pets'])->andReturn([
                    array_merge($data, ['TABLE_NAME' => 'cats', 'TABLE_ROWS' => 42]),
                    array_merge($data, ['TABLE_NAME' => 'dogs', 'TABLE_ROWS' => 69]),
                ]);
        });

        $response = $this->authed()->getJson($this->endpoint('pets'));

        $response->assertOk();
        $response->assertJsonStructure(['name', 'tableCount', 'tables' => [
            ['name', 'engine', 'collation', 'rowCount', 'size'],
        ]]);
        $response->assertJsonFragment(['name' => 'pets']);
        $response->assertJsonFragment([
            'collation' => 'utf8mb4_unicode_ci',
            'engine' => 'InnoDB',
            'name' => 'dogs',
            'rowCount' => 69,
        ]);
    }
}
