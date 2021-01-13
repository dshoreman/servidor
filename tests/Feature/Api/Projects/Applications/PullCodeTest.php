<?php

namespace Tests\Feature\Api\Projects\Applications;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\RequiresAuth;
use Tests\TestCase;

class PullCodeTest extends TestCase
{
    use ArraySubsetAsserts;
    use RefreshDatabase;
    use RequiresAuth;

    private const TEST_REPO = 'dshoreman/servidor-test-site';

    /** @test */
    public function guest_cannot_pull_app_source(): void
    {
        $project = Project::create(['name' => 'Pullable Project']);
        $project->applications()->save($app = new Application([
            'source_provider' => 'github',
            'source_repository' => self::TEST_REPO,
            'template' => 'html',
        ]));

        $response = $this->postJson('/api/projects/' . $project->id . '/apps/' . $app->id . '/pull');

        $response->assertJsonCount(1);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $this->assertArraySubset($project->toArray(), Project::with('applications')->firstOrFail()->toArray());
    }

    /** @test */
    public function authed_user_can_pull_app_source(): void
    {
        $project = Project::create(['name' => 'Pullable Project']);
        $project->applications()->save($app = new Application([
            'source_provider' => 'github',
            'source_repository' => self::TEST_REPO,
            'template' => 'html',
        ]));

        $response = $this->authed()->postJson('/api/projects/' . $project->id . '/apps/' . $app->id . '/pull');

        $response->assertOk();
        $this->assertEquals('/var/www/pullable-project/servidor-test-site', $app->source_root);
        $this->assertDirectoryExists($app->source_root . '/.git');
    }

    /**
     * @test
     * @depends authed_user_can_pull_app_source
     */
    public function can_checkout_after_initial_pull(): void
    {
        // While the directory structure should remain from the previous test,
        // we need to recreate the project here as db is refreshed in between.
        $project = Project::create(['name' => 'Pullable Project']);
        $project->applications()->save($app = new Application([
            'source_provider' => 'github',
            'source_repository' => self::TEST_REPO,
            'template' => 'html',
        ]));

        $response = $this->authed()->postJson('/api/projects/' . $project->id . '/apps/' . $app->id . '/pull');

        $response->assertOk();
        $this->assertEquals('Pullable Project', $response->json()['project']['name']);
        $response->assertJsonFragment([
            'source_root' => '/var/www/pullable-project/servidor-test-site',
            'system_user' => null,
            'template' => 'html',
        ]);
    }

    /** @test */
    public function pull_creates_source_root_if_it_doesnt_exist(): void
    {
        $this->assertDirectoryNotExists($root = '/var/www/initially-appless');
        $project = Project::create(['name' => 'Initially Appless']);
        $project->applications()->save($app = new Application([
            'source_provider' => 'github',
            'source_repository' => self::TEST_REPO,
            'template' => 'html',
        ]));

        $response = $this->authed()->postJson('/api/projects/' . $project->id . '/apps/' . $app->id . '/pull');

        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Initially Appless',
            'source_root' => $root . '/servidor-test-site',
            'template' => 'html',
        ]);
        $this->assertDirectoryExists($root . '/servidor-test-site');
    }

    public static function tearDownAfterClass(): void
    {
        exec('sudo rm -rf /var/www/initially-appless /var/www/pullable-project');
    }
}
