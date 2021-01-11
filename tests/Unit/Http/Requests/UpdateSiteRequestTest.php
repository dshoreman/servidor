<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\UpdateSite;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class UpdateSiteRequestTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(UpdateSite::class);
    }

    /** @test */
    public function site_redirect_type_is_required_when_type_is_redirect(): void
    {
        $data = [
            'type' => 'redirect',
        ];

        $v = $this->getValidator($data);

        $this->assertStringContainsString('required', $v->errors()->first('redirect_type'));
    }

    /** @test */
    public function site_redirect_type_must_be_an_integer(): void
    {
        $this->assertTrue($this->validateField('redirect_type', 301));
        $this->assertFalse($this->validateField('redirect_type', ['a']));
        $this->assertFalse($this->validateField('redirect_type', 'string'));
        $this->assertFalse($this->validateField('redirect_type', (object) ['a']));
    }

    /** @test */
    public function site_redirect_to_is_required_when_type_is_redirect(): void
    {
        $v = $this->getValidator(['type' => 'redirect']);

        $this->assertStringContainsString('required', $v->errors()->first('redirect_to'));
    }

    /** @test */
    public function site_redirect_to_must_be_a_string(): void
    {
        $this->assertTrue($this->validateField('redirect_to', '/'));
        $this->assertFalse($this->validateField('redirect_to', 42));
        $this->assertFalse($this->validateField('redirect_to', true));
        $this->assertFalse($this->validateField('redirect_to', ['a', 'b']));
        $this->assertFalse($this->validateField('redirect_to', (object) ['a', 'b']));
    }
}
