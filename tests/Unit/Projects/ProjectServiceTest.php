<?php

namespace Tests\Unit\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
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
    public function template_can_be_set_with_new_service(): ProjectService
    {
        $service = new ProjectService(['template' => 'php']);

        $this->assertEquals('php', $service->template);

        return $service;
    }

    /** @test */
    public function template_throws_exception_when_invalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid template 'bla'");

        $this->project->services()->save($service = new ProjectService([
            'template' => 'bla',
        ]));
    }

    /** @test */
    public function redirect_attributes_can_be_set_with_new_service(): void
    {
        $service = new ProjectService([
            'domain_name' => 'a',
            'config' => ['redirect' => [
                'target' => 'b',
                'type' => 302,
            ]],
        ]);

        $this->assertEquals('a', $service->domain_name);
        $this->assertEquals('b', $service->config->get('redirect')['target']);
        $this->assertEquals(302, $service->config->get('redirect')['type']);
    }

    /**
     * @test
     *
     * @depends template_can_be_set_with_new_service
     */
    public function can_access_project(ProjectService $service): ProjectService
    {
        $this->assertInstanceOf(BelongsTo::class, $service->project());

        $service->project()->associate($this->project);

        $this->assertEquals('ghosty', $service->project->name);

        return $service;
    }

    /**
     * @test
     *
     * @depends can_access_project
     */
    public function source_repo_is_parsed_correctly(ProjectService $service): void
    {
        /** @phpstan-ignore-next-line */
        $service->config = ['source' => [
            'provider' => 'github',
            'repository' => 'dshoreman/servidor-test-site',
        ]];

        $this->assertEquals('https://github.com/dshoreman/servidor-test-site.git', $service->source_uri);
    }

    /**
     * @test
     *
     * @depends can_access_project
     */
    public function system_user_is_null_when_not_found(ProjectService $service): void
    {
        $this->assertNull($service->systemUser);
    }

    /** @test */
    public function system_user_is_null_when_not_required(): void
    {
        $service = new ProjectService(['template' => 'html']);
        $service->project()->associate($this->project);

        $this->assertNull($service->systemUser);
    }

    /**
     * @test
     *
     * @depends can_access_project
     */
    public function system_user_is_created_on_save(ProjectService $service): void
    {
        $service->project()->associate($this->project);
        $service->save();

        $this->assertArraySubset(['name' => 'ghosty', 'dir' => '/home/ghosty'], $service->systemUser);
    }

    /** @test */
    public function nginx_config_defaults_to_basic_template(): void
    {
        $this->project->services()->save($service = new ProjectService([
            'domain_name' => 'basicdefault.example',
            'config' => ['source' => [
                'provider' => 'github',
                'repository' => 'dshoreman/servidor-test-site',
            ]],
        ]));

        $this->assertFileExists($config = storage_path('app/vhosts/basicdefault.example.conf'));
        $this->assertStringContainsString('index index.html index.htm;', file_get_contents($config));
    }

    /**
     * @test
     *
     * @depends can_access_project
     */
    public function laravel_apps_use_php_nginx_template(ProjectService $service): void
    {
        $service->template = 'laravel';
        $service->domain_name = 'laratest.dev';
        $service->save();

        $this->assertFileExists($p = storage_path('app/vhosts/laratest.dev.conf'));
        $this->assertStringContainsString('index index.php index.html index.htm;', $s = file_get_contents($p));
        $this->assertStringContainsString('try_files $uri $uri/ /index.php?query_string', $s);
    }

    /** @test */
    public function redirect_only_services_use_redirect_template(): void
    {
        $this->project->services()->save($service = new ProjectService([
            'domain_name' => 'a-redir.example',
            'template' => 'redirect',
            'config' => ['redirect' => [
                'target' => 'b-redir.example',
                'type' => 301,
            ]],
        ]));

        $this->assertFileExists($config = storage_path('app/vhosts/a-redir.example.conf'));
        $this->assertStringContainsString('server_name a-redir.example;', $txt = file_get_contents($config));
        $this->assertStringContainsString('return 301 b-redir.example;', $txt = file_get_contents($config));
    }

    public static function tearDownAfterClass(): void
    {
        exec('grep ^ghosty /etc/passwd && sudo userdel -r ghosty 2>/dev/null');
        exec('cd /var/servidor/storage/app/vhosts; sudo rm basicdefault.example.conf laratest.dev.conf a-redir.example.conf');
    }
}
