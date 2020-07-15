<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Validator;
use Servidor\Http\Requests\UpdateSite;
use Servidor\Site;
use Tests\TestCase;

class UpdateSiteRequestTest extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();

        $this->rules = (new UpdateSite)->rules();
        $this->validator = $this->app['validator'];
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
    public function site_type_is_required(): void
    {
        $this->assertFalse($this->validateField('type', ''));
    }

    /** @test */
    public function site_type_must_be_a_valid_type(): void
    {
        $this->assertTrue($this->validateField('type', 'basic'));
        $this->assertTrue($this->validateField('type', 'php'));
        $this->assertTrue($this->validateField('type', 'laravel'));
        $this->assertTrue($this->validateField('type', 'redirect'));
        $this->assertFalse($this->validateField('type', 'invalid'));
    }

    /** @test */
    public function site_source_repo_is_required_when_type_is_not_redirect(): void
    {
        $error = 'The source repo field is required unless type is in redirect.';
        $types = ['basic', 'php', 'laravel'];

        $dataWithout = [
            'name' => 'Test Site',
            'primary_domain' => 'example-without.com',
            'document_root' => '/',
        ];
        $dataWith = array_merge($dataWithout, [
            'source_repo' => 'https://github.com/foo/bar.git',
        ]);

        foreach ($types as $type) {
            $dataWithout['type'] = $type;
            $dataWith['type'] = $type;

            $v = $this->getValidator($dataWithout);
            $this->assertEquals($error, $v->errors()->first('source_repo'));
            $this->assertFalse($v->passes());

            $v = $this->getValidator($dataWith);
            $this->assertArrayNotHasKey('source_repo', $v->errors()->toArray());
            $this->assertTrue($v->passes());
        }
    }

    /** @test */
    public function site_source_repo_is_not_required_when_type_is_redirect(): void
    {
        $data = [
            'name' => 'Test Site',
            'primary_domain' => 'example.com',
            'type' => 'redirect',
            'redirect_type' => 301,
            'redirect_to' => '/',
        ];

        $v = $this->getValidator($data);

        $this->assertEmpty($v->errors()->get('source_repo'));
        $this->assertTrue($v->passes());
    }

    /** @test */
    public function site_source_repo_must_be_a_valid_url(): void
    {
        $this->assertTrue($this->validateField('source_repo', 'https://github.com/foo/bar'));
        $this->assertFalse($this->validateField('source_repo', 'localhost'));
        $this->assertFalse($this->validateField('source_repo', 42));
        $this->assertFalse($this->validateField('source_repo', true));
        $this->assertFalse($this->validateField('source_repo', ['a', 'b']));
    }

    /** @test */
    public function site_document_root_is_required_when_type_is_not_redirect(): void
    {
        $v = $this->getValidator(['type' => 'php']);

        $this->assertStringContainsString('required', $v->errors()->first('document_root'));
        $this->assertFalse($v->passes());

        $v = $this->getValidator([
            'name' => 'foo',
            'primary_domain' => 'localhost',
            'type' => 'php',
            'document_root' => '/',
            'source_repo' => 'https://github.com/foo/bar.git',
        ]);

        $this->assertEmpty($v->errors()->get('document_root'));
        $this->assertTrue($v->passes());
    }

    /** @test */
    public function site_document_root_is_not_required_when_type_is_redirect(): void
    {
        $data = [
            'name' => 'Test Site',
            'primary_domain' => 'example.com',
            'type' => 'redirect',
            'redirect_type' => 301,
            'redirect_to' => '/',
        ];

        $v = $this->getValidator($data);

        $this->assertEmpty($v->errors()->get('document_root'));
        $this->assertTrue($v->passes());
    }

    /** @test */
    public function site_document_root_must_be_a_string(): void
    {
        $this->assertTrue($this->validateField('document_root', '/'));
        $this->assertFalse($this->validateField('document_root', 42));
        $this->assertFalse($this->validateField('document_root', true));
        $this->assertFalse($this->validateField('document_root', ['a', 'b']));
        $this->assertFalse($this->validateField('document_root', (object) ['a', 'b']));
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

    /** @test */
    public function site_is_enabled_must_be_a_boolean(): void
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
        return $this->validator->make($data, $rules ?: $this->rules);
    }
}
