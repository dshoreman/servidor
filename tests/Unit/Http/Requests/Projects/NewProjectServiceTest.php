<?php

namespace Tests\Unit\Http\Requests\Projects;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\Projects\NewProjectService;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class NewProjectServiceTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(NewProjectService::class);
    }

    /** @test */
    public function template_is_required(): void
    {
        $this->validateFieldFails('template', '');
    }

    /** @test */
    public function template_must_be_valid(): void
    {
        $this->validateFieldFails('template', 'basic');
        $this->validateFieldPasses('template', 'html');
        $this->validateFieldPasses('template', 'php');
        $this->validateFieldPasses('template', 'laravel');
        $this->validateFieldPasses('template', 'redirect');
        $this->validateFieldFails('template', 'invalid');
    }

    /** @test */
    public function domain_must_be_valid(): void
    {
        $this->validateFieldFails('domain', 'not a url');
        $this->validateFieldPasses('domain', 'example.com');
    }

    /** @test */
    public function config_must_be_an_array(): void
    {
        $this->validateFieldPasses('config', ['phpVersion' => null]);
        $this->validateFieldFails('config', 'foo');
        $this->validateFieldFails('config', '[]');
        $this->validateFieldFails('config', true);
        $this->validateFieldFails('config', null);
        $this->validateFieldFails('config', []);
        $this->validateFieldFails('config', '');
    }

    /** @test */
    public function redirect_target_is_required_when_type_is_redirect(): void
    {
        $v = $this->getValidator([
            'template' => 'redirect',
            'config' => ['redirect' => ['type' => 301]],
        ]);

        $this->assertStringContainsString('required', $v->errors()->first('config.redirect.target'));
    }

    /** @test */
    public function redirect_target_must_be_a_string(): void
    {
        $this->validateConfigFieldPasses('redirect.target', '/');
        $this->validateConfigFieldFails('redirect.target', 42);
        $this->validateConfigFieldFails('redirect.target', true);
        $this->validateConfigFieldFails('redirect.target', ['a', 'redirects', 'b']);
        $this->validateConfigFieldFails('redirect.target', (object) ['a', 'redirects', 'b']);
    }

    /** @test */
    public function redirect_type_is_required(): void
    {
        $v = $this->getValidator([
            'template' => 'redirect',
            'config' => ['redirect' => ['target' => 'example.com']],
        ]);

        $this->assertStringContainsString('required', $v->errors()->first('config.redirect.type'));
    }

    /** @test */
    public function redirect_type_must_be_an_integer(): void
    {
        $this->validateConfigFieldPasses('redirect.type', 301);
        $this->validateConfigFieldFails('redirect.type', ['a']);
        $this->validateConfigFieldFails('redirect.type', 'string');
        $this->validateConfigFieldFails('redirect.type', (object) ['a']);
    }

    /** @test */
    public function source_provider_must_be_valid(): void
    {
        $this->validateConfigFieldFails('source.provider', 42);
        $this->validateConfigFieldPasses('source.provider', 'github');
        $this->validateConfigFieldFails('source.provider', 'gitlab');
        $this->validateConfigFieldPasses('source.provider', 'bitbucket');
    }

    /** @test */
    public function source_repository_is_required(): void
    {
        $this->validateConfigFieldFails('source.repository', '');
    }

    /** @test */
    public function source_repository_must_be_a_valid_url(): void
    {
        $this->validateConfigFieldPasses('source.repository', 'foo/bar');
        $this->validateConfigFieldFails('source.repository', 'foo/bar.git');
        $this->validateConfigFieldFails('source.repository', 'https://github.com/foo/bar');
        $this->validateConfigFieldFails('source.repository', 'localhost');
        $this->validateConfigFieldFails('source.repository', 42);
        $this->validateConfigFieldFails('source.repository', true);
        $this->validateConfigFieldFails('source.repository', ['a', 'b']);
    }

    /** @test */
    public function php_version_must_be_valid(): void
    {
        $this->validateConfigFieldPasses('phpVersion', '7.0');
        $this->validateConfigFieldPasses('phpVersion', '7.1');
        $this->validateConfigFieldPasses('phpVersion', '7.2');
        $this->validateConfigFieldPasses('phpVersion', '7.3');
        $this->validateConfigFieldPasses('phpVersion', '7.4');
        $this->validateConfigFieldPasses('phpVersion', '8.0');
        $this->validateConfigFieldPasses('phpVersion', '8.1');

        $this->validateConfigFieldFails('phpVersion', '6.9');
        $this->validateConfigFieldFails('phpVersion', 'foo');
        $this->validateConfigFieldFails('phpVersion', false);
        $this->validateConfigFieldFails('phpVersion', true);
        $this->validateConfigFieldFails('phpVersion', null);
        $this->validateConfigFieldFails('phpVersion', 80);
    }
}
