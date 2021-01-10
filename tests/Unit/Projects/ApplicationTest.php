<?php

namespace Tests\Unit\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Application;
use Servidor\Projects\Project;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use ArraySubsetAsserts;
    use PrunesDeletables;
    use RefreshDatabase;
    use RequiresAuth;

    /** @var Project */
    private $project;

    public function setUp(): void
    {
        parent::setUp();

        $this->project = Project::create(['name' => 'ghosty']);
    }

    /** @test */
    public function can_set_template_with_new_application(): Application
    {
        $app = new Application(['template' => 'php']);

        $this->assertEquals('php', $app->template);

        return $app;
    }

    /**
     * @test
     * @depends can_set_template_with_new_application
     */
    public function can_access_project(Application $app): Application
    {
        $app->project()->associate($this->project);

        $this->assertEquals('ghosty', $app->project->name);

        return $app;
    }

    /**
     * @test
     * @depends can_access_project
     */
    public function source_repo_is_parsed_correctly(Application $app): void
    {
        $app->source_provider = 'github';
        $app->source_repository = 'foo/bar';

        $this->assertEquals('https://github.com/foo/bar.git', $app->source_uri);
    }

    /**
     * @test
     * @depends can_access_project
     */
    public function system_user_is_null_when_not_found(Application $app): void
    {
        $this->assertNull($app->systemUser);
    }

    /** @test */
    public function system_user_is_null_when_not_required(): void
    {
        $app = new Application(['template' => 'html']);
        $app->project()->associate($this->project);

        $this->assertNull($app->systemUser);
    }

    /**
     * @test
     * @depends can_access_project
     */
    public function system_user_is_created_on_save(Application $app): void
    {
        $app->project()->associate($this->project);
        $app->save();

        $this->assertArraySubset(['name' => 'ghosty', 'dir' => '/home/ghosty'], $app->systemUser);
    }

    public static function tearDownAfterClass(): void
    {
        exec('grep ^ghosty /etc/passwd && sudo userdel -r ghosty 2>/dev/null');
    }
}
