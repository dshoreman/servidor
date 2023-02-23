<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;
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
    public function listed_projects_include_applications(): array
    {
        $project = Project::create(['name' => 'Laratest']);
        $project->applications()->save(new Application(['template' => 'laravel']));

        $response = $this->authed()->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJson(Project::with('applications')->get()->toArray());

        return $response->json()[0];
    }

    /**
     * @test
     *
     * @depends listed_projects_include_applications
     */
    public function project_applications_include_list_of_logs($project): void
    {
        $app = $project['applications'][0];

        $this->assertArrayHasKey('logs', $app);
        $this->assertArraySubset(['php' => 'PHP Error Log'], $app['logs']);
        $this->assertArraySubset(['laravel' => 'Laravel Log'], $app['logs']);
    }

    /** @test */
    public function listed_projects_include_redirects(): array
    {
        $project = Project::create(['name' => 'Redirtest']);
        $project->redirects()->save(new Redirect([
            'domain_name' => 'a',
            'config' => ['redirect' => [
                'target' => 'b',
                'type' => 301,
            ]],
        ]));

        $response = $this->authed()->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJson(Project::with('redirects')->get()->toArray());

        return $response->json()[0];
    }
}
