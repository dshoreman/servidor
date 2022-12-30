<?php

namespace Tests\Unit\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Exception;
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
    public function template_can_be_set_with_new_application(): Application
    {
        $app = new Application(['template' => 'php']);

        $this->assertEquals('php', $app->template);

        return $app;
    }

    /** @test */
    public function template_throws_exception_when_invalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid template 'bla'");

        $this->project->applications()->save($app = new Application([
            'template' => 'bla',
        ]));
    }

    /**
     * @test
     *
     * @depends template_can_be_set_with_new_application
     */
    public function can_access_project(Application $app): Application
    {
        $app->project()->associate($this->project);

        $this->assertEquals('ghosty', $app->project->name);

        return $app;
    }

    /**
     * @test
     *
     * @depends can_access_project
     */
    public function source_repo_is_parsed_correctly(Application $app): void
    {
        $app->source_provider = 'github';
        $app->source_repository = 'dshoreman/servidor-test-site';

        $this->assertEquals('https://github.com/dshoreman/servidor-test-site.git', $app->source_uri);
    }

    /**
     * @test
     *
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
     *
     * @depends can_access_project
     */
    public function system_user_is_created_on_save(Application $app): void
    {
        $app->project()->associate($this->project);
        $app->save();

        $this->assertArraySubset(['name' => 'ghosty', 'dir' => '/home/ghosty'], $app->systemUser);
    }

    /** @test */
    public function nginx_config_defaults_to_basic_template(): void
    {
        $this->project->applications()->save($app = new Application([
            'domain_name' => 'basicdefault.example',
            'source_provider' => 'github',
            'source_repository' => 'dshoreman/servidor-test-site',
        ]));

        $this->assertFileExists($config = storage_path('app/vhosts/basicdefault.example.conf'));
        $this->assertStringContainsString('index index.html index.htm;', file_get_contents($config));
    }

    /**
     * @test
     *
     * @depends can_access_project
     */
    public function laravel_apps_use_php_nginx_template(Application $app): void
    {
        $app->template = 'laravel';
        $app->domain_name = 'laratest.dev';
        $app->save();

        $this->assertFileExists($p = storage_path('app/vhosts/laratest.dev.conf'));
        $this->assertStringContainsString('index index.php index.html index.htm;', $s = file_get_contents($p));
        $this->assertStringContainsString('try_files $uri $uri/ /index.php?query_string', $s);
    }

    public static function tearDownAfterClass(): void
    {
        exec('grep ^ghosty /etc/passwd && sudo userdel -r ghosty 2>/dev/null');
        exec('cd /var/servidor/storage/app/vhosts; sudo rm basicdefault.example.conf laratest.dev.conf');
    }
}
