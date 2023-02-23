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
            'config' => ['redirect' => ['target' => 'example.com']],
        ]]]);

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
    public function redirect_target_is_required_when_type_is_redirect(): void
    {
        $v = $this->getValidator(['name' => 'redir', 'redirects' => [[
            'config' => ['redirect' => ['type' => 301]],
        ]]]);

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
}
