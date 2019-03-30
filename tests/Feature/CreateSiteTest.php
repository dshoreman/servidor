<?php

namespace Tests\Feature;

use Servidor\Site;
use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGuestCanCreateSite()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Test Site',
            'primary_domain' => 'http://example.com',
            'is_enabled' => true,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            'name' => 'Test Site',
            'primary_domain' => 'http://example.com',
            'is_enabled' => true,
        ]);

        $site = Site::first();
        $this->assertEquals('Test Site', $site->name);
        $this->assertEquals('http://example.com', $site->primary_domain);
        $this->assertTrue($site->is_enabled);
    }

    public function testNameIsRequired()
    {
        $response = $this->postJson('/api/sites', ['name' => '']);

        $this->assertValidationErrors($response, 'name');

        $this->assertNull(Site::first());
    }

    public function testNameMustBeString()
    {
        $response = $this->postJson('/api/sites', ['name' => 42]);

        $this->assertValidationErrors($response, 'name');

        $this->assertNull(Site::first());
    }

    public function testNameMustBeUnique()
    {
        Site::create(['name' => 'Duplicate me!']);

        $response = $this->postJson('/api/sites', ['name' => 'Duplicate me!']);

        $this->assertValidationErrors($response, 'name');

        $this->assertEquals(1, Site::count());
    }

    public function testPrimaryDomainMustBeUrl()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Good name',
            'primary_domain' => 'not a url',
        ]);

        $this->assertValidationErrors($response, 'primary_domain');

        $this->assertNull(Site::first());
    }

    public function testIsEnabledMustBeBoolean()
    {
        $response = $this->postJson('/api/sites', [
            'name' => 'Good name',
            'is_enabled' => 'true',
        ]);

        $this->assertValidationErrors($response, 'is_enabled');

        $this->assertNull(Site::first());
    }
}
