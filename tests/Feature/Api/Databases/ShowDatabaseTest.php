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
        $response->assertJsonStructure(['name', 'tableCount', 'tables' => [['name']]]);
        $response->assertJsonFragment(['name' => 'servidor']);
    }
}
