<?php

namespace Tests\Feature\Api\Sites;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\RequiresAuth;
use Tests\TestCase;

class ViewLogTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_retrieve_log_file(): void
    {
        $app = new Application(['template' => 'php']);
        $project = Project::create(['name' => 'logtest']);
        $project->applications()->save($app);

        $response = $this->authed()->getJson('/api/projects/' . $project->id . '/logs/php.app-' . $app->id . '.log');

        $response->assertOk();
    }
}
