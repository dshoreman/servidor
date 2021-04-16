<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\Projects\NewProjectApp;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class NewProjectAppTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(NewProjectApp::class);
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
        $this->validateFieldFails('template', 'redirect');
        $this->validateFieldFails('template', 'invalid');
    }

    /** @test */
    public function domain_must_be_valid(): void
    {
        $this->validateFieldFails('domain', 'not a url');
        $this->validateFieldPasses('domain', 'example.com');
    }

    /** @test */
    public function provider_must_be_valid(): void
    {
        $this->validateFieldFails('provider', 42);
        $this->validateFieldPasses('provider', 'github');
        $this->validateFieldFails('provider', 'gitlab');
        $this->validateFieldPasses('provider', 'bitbucket');
    }

    /** @test */
    public function repository_is_required(): void
    {
        $this->validateFieldFails('repository', '');
    }

    /** @test */
    public function repository_must_be_a_valid_url(): void
    {
        $this->validateFieldPasses('repository', 'foo/bar');
        $this->validateFieldFails('repository', 'foo/bar.git');
        $this->validateFieldFails('repository', 'https://github.com/foo/bar');
        $this->validateFieldFails('repository', 'localhost');
        $this->validateFieldFails('repository', 42);
        $this->validateFieldFails('repository', true);
        $this->validateFieldFails('repository', ['a', 'b']);
    }
}