<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\RequiresAuth;
use Tests\TestCase;

class CreateProjectServiceTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    protected $endpoint = '/api/projects/{id}/services';

    /** @test */
    public function can_create_project_service(): void
    {
        $project = Project::create(['name' => 'Project with App']);

        $response = $this->authed()->postJson($this->endpoint($project->id), [
            'template' => 'php',
            'domain' => 'example.com',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['template', 'domain_name', 'config' => ['source' => ['provider']]]);
        $response->assertJsonFragment([
            'template' => 'php',
            'domain_name' => 'example.com',
            'source_uri' => 'https://github.com/dshoreman/servidor-test-site.git',
            'source_root' => '/home/project-with-app/servidor-test-site',
        ]);

        $project = Project::with('services')->firstOrFail();
        $this->assertInstanceOf(Collection::class, $project->services);

        $service = $project->services->first();
        $this->assertInstanceOf(ProjectService::class, $service);
        $this->assertArraySubset(['template' => 'php', 'domain_name' => 'example.com'], $service->toArray());
    }

    /** @test */
    public function cannot_create_project_app_when_repository_not_found(): void
    {
        $project = Project::create(['name' => 'Project with Repo 404']);

        $response = $this->authed()->postJson($this->endpoint($project->id), [
            'template' => 'php',
            'config' => ['source' => [
                'provider' => 'custom',
                'repository' => 'some/missing-test-site',
            ]],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('config.source.repository');
        $response->assertJsonFragment(['config.source.repository' => [
            "This repo couldn't be found. Does it require auth?",
        ]]);
    }

    /** @test */
    public function cannot_create_project_app_when_branch_not_found(): void
    {
        $project = Project::create(['name' => 'Project with Branch 404']);

        $response = $this->authed()->postJson($this->endpoint($project->id), [
            'template' => 'html',
            'config' => ['source' => [
                'branch' => 'unicoorn',
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('config.source.branch');
        $response->assertJsonFragment(['config.source.branch' => [
            "This branch doesn't exist.",
        ]]);
    }
}
