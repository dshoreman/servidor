<?php

namespace Tests\Feature\Api\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Servidor\Projects\Project;
use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateProjectTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    public function tearDown(): void
    {
        parent::tearDown();

        exec('sudo rm -f /etc/nginx/sites-*/*-symlink.test.conf');
    }

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
    public function authed_user_can_rename_project(): array
    {
        $project = Project::create(['name' => 'My Other Blog']);

        $response = $this->authed()->putJson('/api/projects/' . $project->id, [
            'name' => 'My Updated Blog',
        ]);

        $updated = Project::findOrFail($project->id);

        $response->assertOk();
        $this->assertEquals('My Updated Blog', $updated->name);

        return $response->json();
    }

    /**
     * @test
     *
     * @depends authed_user_can_rename_project
     */
    public function update_response_includes_applications(array $data): void
    {
        $this->assertArrayHasKey('applications', $data);
        $this->assertEmpty($data['applications']);
    }

    /**
     * @test
     *
     * @depends authed_user_can_rename_project
     */
    public function update_response_includes_redirects(array $data): void
    {
        $this->assertArrayHasKey('redirects', $data);
        $this->assertEmpty($data['redirects']);
    }

    /** @test */
    public function project_can_be_enabled(): void
    {
        $project = Project::create(['name' => 'My Enabling Blog']);

        $response = $this->authed()->putJson('/api/projects/' . $project->id, [
            'name' => 'My Enabled Blog',
            'is_enabled' => true,
        ]);

        $updated = Project::findOrFail($project->id);

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'My Enabled Blog',
            'is_enabled' => true,
        ]);
    }

    /**
     * @test
     *
     * @dataProvider symlinkProvider
     */
    public function updating_project_also_toggles_symlink(string $type, array $data): void
    {
        $name = $type . ' Symlink Test';
        $domain = $type . '-symlink.test';

        $this->assertFileDoesNotExist("/etc/nginx/sites-available/{$domain}.conf");
        $this->assertFileDoesNotExist("/etc/nginx/sites-enabled/{$domain}.conf");
        $project = new Project(['name' => $name, 'is_enabled' => true]);
        $project->save();

        $this->authed()->postJson(
            '/api/projects/' . $project->id . '/' . Str::plural($type),
            array_merge(['domain' => $domain], $data),
        );
        $this->assertFileExists("/etc/nginx/sites-available/{$domain}.conf");
        $this->assertFileExists("/etc/nginx/sites-enabled/{$domain}.conf");

        $response = $this->authed()->putJson("/api/projects/{$project->id}", [
            'is_enabled' => false,
        ]);

        $response->assertOk();
        $response->assertJsonFragment(['name' => $name, 'is_enabled' => false]);
        $this->assertFileExists("/etc/nginx/sites-available/{$domain}.conf");
        $this->assertFileDoesNotExist("/etc/nginx/sites-enabled/{$domain}.conf");
    }

    public function symlinkProvider(): array
    {
        return [
            'Project with an application' => ['app', [
                'template' => 'html',
                'config' => ['source' => [
                    'provider' => 'github',
                    'repository' => 'dshoreman/servidor-test-site',
                    'branch' => 'develop',
                ]],
            ]],
            'Project with a redirect' => ['redirect', [
                'config' => ['redirect' => [
                    'target' => 'example.com',
                    'type' => 301,
                ]],
            ]],
        ];
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
