<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;
use Tests\RequiresAuth;
use Tests\TestCase;

class CreateProjectRedirectTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    protected $endpoint = '/api/projects/{id}/redirects';

    /** @test */
    public function can_create_project_with_redirect(): void
    {
        $project = Project::create(['name' => 'Project with Redirect']);

        $response = $this->authed()->postJson($this->endpoint($project->id), [
            'domain' => 'example.com',
            'config' => ['redirect' => [
                'target' => 'https://example.com',
                'type' => 301,
            ]],
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['domain_name', 'config' => [
            'redirect' => ['target', 'type'],
        ]]);
        $response->assertJsonFragment([
            'domain_name' => 'example.com',
            'config' => ['redirect' => [
                'target' => 'https://example.com',
                'type' => 301,
            ]],
        ]);

        $project = Project::with('redirects')->firstOrFail();
        $this->assertInstanceOf(Collection::class, $project->redirects);

        $redirect = $project->redirects->first();
        $this->assertInstanceOf(Redirect::class, $redirect);
        $this->assertArraySubset(['config' => ['redirect' => [
            'type' => 301, 'target' => 'https://example.com'],
        ]], $redirect->toArray());
    }
}
