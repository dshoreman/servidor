<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Site;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function system_user_is_null_when_not_found(): void
    {
        $site = Site::create(['name' => 'ghosty']);
        $site->system_user = 2342;

        $this->assertEquals('ghosty', $site->name);
        $this->assertNull($site->systemUser);
    }
}
