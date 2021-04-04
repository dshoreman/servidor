<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\RequiresAuth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /** @test */
    public function user_can_logout(): void
    {
        $response = $this->authed()->deleteJson('/api/session');

        $response->assertNoContent();
    }
}
