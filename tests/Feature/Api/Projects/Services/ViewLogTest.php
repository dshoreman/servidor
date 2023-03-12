<?php

namespace Tests\Feature\Api\Projects\Services;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\RequiresAuth;
use Tests\TestCase;

class ViewLogTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function can_retrieve_log_file(): void
    {
        $service = new ProjectService(['template' => 'php']);
        $project = Project::create(['name' => 'logtest']);
        $project->services()->save($service);

        $response = $this->authed()->getJson("/api/projects/{$project->id}/logs/php.service-{$service->id}.log");

        $response->assertOk();
    }

    /** @test */
    public function cannot_get_log_if_project_id_does_not_match(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Project mismatch');

        $service1 = new ProjectService(['template' => 'php']);
        $service2 = new ProjectService(['template' => 'laravel']);
        $project1 = Project::create(['name' => 'mismatch1']);
        $project2 = Project::create(['name' => 'mismatch2']);
        $project1->services()->save($service1);
        $project2->services()->save($service1);

        $this->withoutExceptionHandling();
        $response = $this->authed()->getJson("/api/projects/{$project1->id}/logs/php.service-{$service1->id}.log");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->withExceptionHandling();
    }

    public static function tearDownAfterClass(): void
    {
        exec('sudo userdel mismatch1; sudo rm -rf /home/mismatch1');
        exec('sudo userdel logtest; sudo rm -rf /home/logtest');
    }
}
