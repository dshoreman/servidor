<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\RequiresAuth;
use Tests\TestCase;

class RemoveProjectTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function guest_cannot_delete_project(): void
    {
        $project = Project::create(['name' => 'Primed for deletion']);

        $response = $this->deleteJson('/api/projects/' . $project->id);

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);

        self::assertArraySubset(
            $project->toArray(),
            Project::with(['services'])->firstOrFail()->toArray(),
        );
    }

    /** @test */
    public function authed_user_can_delete_project(): void
    {
        $project = Project::create(['name' => 'Delete me!']);

        $response = $this->authed()->deleteJson('/api/projects/' . $project->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertNull(Project::find($project->id));
    }

    /** @test */
    public function project_with_service_can_still_be_deleted(): void
    {
        $project = Project::create(['name' => 'Delete me again!']);
        $service = $project->services()->create([
            'template' => 'html',
        ]);

        $response = $this->authed()->deleteJson('/api/projects/' . $project->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertNull(Project::find($project->id));
        $this->assertNull(ProjectService::find($service->id));
    }
}
