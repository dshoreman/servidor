<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\Projects\NewProjectRedirect;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class NewProjectRedirectTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(NewProjectRedirect::class);
    }

    /** @test */
    public function redirect_type_is_required(): void
    {
        $v = $this->getValidator(['name' => 'redir', 'redirects' => [[
            'target' => 'example.com',
        ]]]);

        $this->assertStringContainsString('required', $v->errors()->first('type'));
    }

    /** @test */
    public function redirect_type_must_be_an_integer(): void
    {
        $this->validateFieldPasses('type', 301);
        $this->validateFieldFails('type', ['a']);
        $this->validateFieldFails('type', 'string');
        $this->validateFieldFails('type', (object) ['a']);
    }

    /** @test */
    public function redirect_target_is_required_when_type_is_redirect(): void
    {
        $v = $this->getValidator(['name' => 'redir', 'redirects' => [[
            'type' => 301,
        ]]]);

        $this->assertStringContainsString('required', $v->errors()->first('target'));
    }

    /** @test */
    public function redirect_target_must_be_a_string(): void
    {
        $this->validateFieldPasses('target', '/');
        $this->validateFieldFails('target', 42);
        $this->validateFieldFails('target', true);
        $this->validateFieldFails('target', ['a', 'redirects', 'b']);
        $this->validateFieldFails('target', (object) ['a', 'redirects', 'b']);
    }
}
