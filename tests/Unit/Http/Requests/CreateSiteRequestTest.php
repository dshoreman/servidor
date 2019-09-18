<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Validator;
use Servidor\Http\Requests\CreateSite;
use Servidor\Site;
use Tests\TestCase;

class CreateSiteRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    private $rules;

    /**
     * @var \Illuminate\Foundation\Application
     */
    private $validator;

    public function setUp()
    {
        parent::setUp();

        $this->rules = (new CreateSite)->rules();
        $this->validator = $this->app['validator'];
    }

    /** @test */
    public function site_name_is_required()
    {
        $this->assertFalse($this->validateField('name', ''));
        $this->assertTrue($this->validateField('name', 'A name'));
    }

    /** @test */
    public function site_name_must_be_a_string()
    {
        $this->assertFalse($this->validateField('name', true));
        $this->assertFalse($this->validateField('name', 42));
        $this->assertFalse($this->validateField('name', []));
    }

    /** @test */
    public function site_name_must_be_unique()
    {
        Site::create(['name' => 'Duplicate me!']);

        $this->assertFalse($this->validateField('name', 'Duplicate me!'));
        $this->assertEquals(1, Site::count());
    }

    /** @test */
    public function site_primary_domain_must_be_a_valid_domain()
    {
        $this->assertFalse($this->validateField('primary_domain', 'not a url'));
        $this->assertTrue($this->validateField('primary_domain', 'example.com'));
    }

    /** @test */
    public function site_is_enabled_must_be_a_boolean()
    {
        $this->assertFalse($this->validateField('is_enabled', 'yes'));
        $this->assertTrue($this->validateField('is_enabled', true));
    }

    private function validateAll(array $data): bool
    {
        return $this->getValidator($data)->passes();
    }

    private function validateField(string $field, $value): bool
    {
        return $this->getValidator(
            [$field => $value],
            [$field => $this->rules[$field]],
        )->passes();
    }

    private function getValidator(array $data, array $rules = []): Validator
    {
        return $this->validator->make($data, $rules ?? $this->rules);
    }
}
