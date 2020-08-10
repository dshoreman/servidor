<?php

namespace Tests\Feature\Api\Sites;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Site;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListSitesTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    protected $endpoint = '/api/sites';

    /** @test */
    public function sites_include_list_of_logs(): void
    {
        $site = Site::create(['name' => 'php-site', 'type' => 'php']);

        $response = $this->authed()->getJson($this->endpoint);

        $response->assertOk();
        $response->assertJsonStructure([['name', 'type', 'logs' => [
            'php' => ['name', 'path'],
        ]]]);
    }
}
