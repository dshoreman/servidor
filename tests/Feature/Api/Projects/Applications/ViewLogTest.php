<?php

namespace Tests\Feature\Api\Projects\Applications;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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

        $response = $this->authed()->getJson("/api/projects/{$project->id}/logs/php.app-{$app->id}.log");

        $response->assertOk();
    }

    /** @test */
    public function cannot_get_log_if_project_id_does_not_match(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Project mismatch');

        $app1 = new Application(['template' => 'php']);
        $app2 = new Application(['template' => 'laravel']);
        $project1 = Project::create(['name' => 'mismatch1']);
        $project2 = Project::create(['name' => 'mismatch2']);
        $project1->applications()->save($app1);
        $project2->applications()->save($app1);

        $this->withoutExceptionHandling();
        $response = $this->authed()->getJson("/api/projects/{$project1->id}/logs/php.app-{$app1->id}.log");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->withExceptionHandling();
    }
}
