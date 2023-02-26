<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\RequiresAuth;
use Tests\TestCase;

class ListProjectsTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    protected $endpoint = '/api/projects';

    /** @test */
    public function guest_cannot_list_projects(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function authed_user_can_list_projects(): void
    {
        Project::create(['name' => 'Blog 1']);
        Project::create(['name' => 'Blog 2']);

        $response = $this->authed()->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson(Project::all()->toArray());
    }

    /** @test */
    public function listed_projects_include_services(): array
    {
        $project = Project::create(['name' => 'Laratest']);
        $project->services()->save(new ProjectService(['template' => 'laravel']));

        $response = $this->authed()->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJson(Project::with('services')->get()->toArray());

        return $response->json()[0];
    }

    /**
     * @test
     *
     * @depends listed_projects_include_services
     */
    public function project_services_include_list_of_logs($project): void
    {
        $service = $project['services'][0];

        $this->assertArrayHasKey('logs', $service);
        $this->assertArraySubset(['php' => 'PHP Error Log'], $service['logs']);
        $this->assertArraySubset(['laravel' => 'Laravel Log'], $service['logs']);
    }

    /** @test */
    public function listed_projects_include_redirects(): array
    {
        $project = Project::create(['name' => 'Redirtest']);
        $project->services()->save(new ProjectService([
            'domain_name' => 'a',
            'template' => 'redirect',
            'config' => ['redirect' => [
                'target' => 'b',
                'type' => 301,
            ]],
        ]));

        $response = $this->authed()->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJson(Project::with('services')->get()->toArray());

        return $response->json()[0];
    }
}
