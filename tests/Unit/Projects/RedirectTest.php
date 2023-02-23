<?php

namespace Tests\Unit\Projects;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;
use Tests\PrunesDeletables;
use Tests\RequiresAuth;
use Tests\TestCase;

class RedirectTest extends TestCase
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

        $this->project = Project::create(['name' => 'redir']);
    }

    /** @test */
    public function attributes_can_be_set_with_new_redirect(): Redirect
    {
        $redirect = new Redirect([
            'domain_name' => 'a',
            'config' => ['redirect' => [
                'target' => 'b',
                'type' => 302,
            ]],
        ]);

        $this->assertEquals('a', $redirect->domain_name);
        $this->assertEquals('b', $redirect->config->get('redirect')['target']);
        $this->assertEquals(302, $redirect->config->get('redirect')['type']);

        return $redirect;
    }

    /**
     * @test
     *
     * @depends attributes_can_be_set_with_new_redirect
     */
    public function can_access_project(Redirect $redirect): Redirect
    {
        $this->assertInstanceOf(BelongsTo::class, $redirect->project());

        $redirect->project()->associate($this->project);

        $this->assertEquals('redir', $redirect->project->name);

        return $redirect;
    }

    /** @test */
    public function nginx_config_uses_redirect_template(): void
    {
        $this->project->applications()->save($redirect = new Redirect([
            'domain_name' => 'a-redir.example',
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
        exec('grep ^redir /etc/passwd && sudo userdel -r redir 2>/dev/null');
        exec('cd /var/servidor/storage/app/vhosts; sudo rm a-redir.example.conf laratest.dev.conf');
    }
}
