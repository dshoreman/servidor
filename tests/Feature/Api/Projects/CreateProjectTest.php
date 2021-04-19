<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Tests\RequiresAuth;
use Tests\TestCase;

class CreateProjectTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    protected $endpoint = '/api/projects';

    /** @test */
    public function guest_cannot_create_project(): void
    {
        $response = $this->postJson('/api/projects', ['name' => 'Test Project']);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(0, Project::count());
        $this->assertEquals(null, Project::first());
    }

    /** @test */
    public function authed_user_can_create_project(): void
    {
        $response = $this->authed()->postJson('/api/projects', [
            'name' => 'Test Project',
            'is_enabled' => true,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'name' => 'Test Project',
            'is_enabled' => true,
        ]);

        $project = Project::firstOrFail();
        $this->assertEquals('Test Project', $project->name);
        $this->assertTrue($project->is_enabled);
    }

    /** @test */
    public function cannot_create_project_without_name(): void
    {
        $response = $this->authed()->postJson($this->endpoint, ['name' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
        $this->assertNull(Project::first());
    }

    /** @test */
    public function cannot_create_project_with_invalid_data(): void
    {
        $response = $this->authed()->postJson($this->endpoint, [
            'name' => '',
            'is_enabled' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
            'is_enabled',
        ]);
        $this->assertNull(Project::first());
    }
}
