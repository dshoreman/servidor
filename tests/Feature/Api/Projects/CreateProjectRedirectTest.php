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
            'type' => 301,
            'domain' => 'example.com',
            'target' => 'https://example.com',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['domain_name', 'target', 'type']);
        $response->assertJsonFragment([
            'type' => 301,
            'domain_name' => 'example.com',
            'target' => 'https://example.com',
        ]);

        $project = Project::with('redirects')->firstOrFail();
        $this->assertInstanceOf(Collection::class, $project->redirects);

        $redirect = $project->redirects->first();
        $this->assertInstanceOf(Redirect::class, $redirect);
        $this->assertArraySubset(['type' => 301, 'target' => 'https://example.com'], $redirect->toArray());
    }
}
