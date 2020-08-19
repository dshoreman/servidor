<?php

namespace Tests\Feature\Api\Sites;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Site;
use Tests\RequiresAuth;
use Tests\TestCase;

class ViewSiteLogsTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_retrieve_content_for_site_log(): void
    {
        $site = Site::create(['name' => 'logtest', 'type' => 'php']);

        $response = $this->authed()->getJson('/api/sites/' . $site->id . '/logs/php');

        $response->assertOk();
    }
}
