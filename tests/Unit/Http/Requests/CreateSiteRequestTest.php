<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\CreateSite;
use Servidor\Site;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class CreateSiteRequestTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(CreateSite::class);
    }

    /** @test */
    public function site_name_is_required(): void
    {
        $this->assertFalse($this->validateField('name', ''));
        $this->assertTrue($this->validateField('name', 'A name'));
    }

    /** @test */
    public function site_name_must_be_a_string(): void
    {
        $this->assertFalse($this->validateField('name', true));
        $this->assertFalse($this->validateField('name', 42));
        $this->assertFalse($this->validateField('name', []));
    }

    /** @test */
    public function site_name_must_be_unique(): void
    {
        Site::create(['name' => 'Duplicate me!']);

        $this->assertFalse($this->validateField('name', 'Duplicate me!'));
        $this->assertEquals(1, Site::count());
    }

    /** @test */
    public function site_primary_domain_must_be_a_valid_domain(): void
    {
        $this->assertFalse($this->validateField('primary_domain', 'not a url'));
        $this->assertTrue($this->validateField('primary_domain', 'example.com'));
    }

    /** @test */
    public function site_is_enabled_must_be_a_boolean(): void
    {
        $this->assertFalse($this->validateField('is_enabled', 'yes'));
        $this->assertTrue($this->validateField('is_enabled', true));
    }
}
