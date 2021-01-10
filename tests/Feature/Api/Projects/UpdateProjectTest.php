<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateProjectTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function guest_cannot_update_project(): void
    {
        $project = Project::create(['name' => 'My Blog']);

        $response = $this->putJson('/api/projects/' . $project->id, [
            'name' => 'My Updated Blog',
        ]);

        $this->assertEquals('My Blog', Project::findOrFail($project->id)->name);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function authed_user_can_rename_project(): void
    {
        $project = Project::create(['name' => 'My Other Blog']);

        $response = $this->authed()->putJson('/api/projects/' . $project->id, [
            'name' => 'My Updated Blog',
        ]);

        $updated = Project::findOrFail($project->id);

        $response->assertOk();
        $this->assertEquals('My Updated Blog', $updated->name);
    }

    /** @test */
    public function can_update_project_while_retaining_the_same_name(): void
    {
        $project = Project::create(['name' => 'My New Blog']);

        $response = $this->putJson('/api/projects/' . $project->id, [
            'name' => 'My New Blog',
        ]);

        $response->assertJsonMissingValidationErrors(['name']);
    }
}
