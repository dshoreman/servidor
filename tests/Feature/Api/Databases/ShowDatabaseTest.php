<?php

namespace Tests\Feature\Api\Databases;

use Tests\RequiresAuth;
use Tests\TestCase;

class ShowDatabaseTest extends TestCase
{
    use RequiresAuth;

    protected $endpoint = '/api/databases/{id}';

    public function testItCanListTables(): void
    {
        $response = $this->authed()->getJson($this->endpoint('servidor'));

        $response->assertOk();
        $response->assertJsonStructure(['name', 'tableCount', 'tables' => [
            ['name', 'engine', 'collation', 'rowCount', 'size'],
        ]]);
        $response->assertJsonFragment(['name' => 'servidor']);
        $response->assertJsonFragment([
            'collation' => 'utf8mb4_unicode_ci',
            'engine' => 'InnoDB',
            'name' => 'failed_jobs',
        ]);
    }
}
