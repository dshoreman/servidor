<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\CreateProjectRequest;
use Servidor\Projects\Project;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class CreateProjectRequestTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(CreateProjectRequest::class);
    }

    /** @test */
    public function name_is_required(): void
    {
        $this->validateFieldFails('name', '');
        $this->validateFieldPasses('name', 'A name');
    }

    /** @test */
    public function name_must_be_a_string(): void
    {
        $this->validateFieldFails('name', true);
        $this->validateFieldFails('name', 42);
        $this->validateFieldFails('name', []);
    }

    /** @test */
    public function name_must_be_unique(): void
    {
        Project::create(['name' => 'Duplicate me!']);

        $this->validateFieldFails('name', 'Duplicate me!');
        $this->assertEquals(1, Project::count());
    }

    /** @test */
    public function is_enabled_must_be_a_boolean(): void
    {
        $this->validateFieldFails('is_enabled', 'yes');
        $this->validateFieldPasses('is_enabled', true);
    }

    /** @test */
    public function app_template_is_required(): void
    {
        $this->validateChildFieldFails('template', 'applications', '');
    }

    /** @test */
    public function app_template_must_be_valid(): void
    {
        $this->validateChildFieldFails('template', 'applications', 'basic');
        $this->validateChildFieldPasses('template', 'applications', 'html');
        $this->validateChildFieldPasses('template', 'applications', 'php');
        $this->validateChildFieldPasses('template', 'applications', 'laravel');
        $this->validateChildFieldFails('template', 'applications', 'redirect');
        $this->validateChildFieldFails('template', 'applications', 'invalid');
    }

    /** @test */
    public function app_domain_must_be_valid(): void
    {
        $this->validateChildFieldFails('domain', 'applications', 'not a url');
        $this->validateChildFieldPasses('domain', 'applications', 'example.com');
    }

    /** @test */
    public function app_provider_must_be_valid(): void
    {
        $this->validateChildFieldFails('provider', 'applications', 42);
        $this->validateChildFieldPasses('provider', 'applications', 'github');
        $this->validateChildFieldFails('provider', 'applications', 'gitlab');
        $this->validateChildFieldPasses('provider', 'applications', 'bitbucket');
    }

    /** @test */
    public function app_repository_is_required(): void
    {
        $this->validateChildFieldFails('repository', 'applications', '');
    }

    /** @test */
    public function app_repository_must_be_a_valid_url(): void
    {
        $this->validateChildFieldPasses('repository', 'applications', 'foo/bar');
        $this->validateChildFieldFails('repository', 'applications', 'foo/bar.git');
        $this->validateChildFieldFails('repository', 'applications', 'https://github.com/foo/bar');
        $this->validateChildFieldFails('repository', 'applications', 'localhost');
        $this->validateChildFieldFails('repository', 'applications', 42);
        $this->validateChildFieldFails('repository', 'applications', true);
        $this->validateChildFieldFails('repository', 'applications', ['a', 'b']);
    }
}
