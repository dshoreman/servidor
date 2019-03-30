<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertValidationErrors($response, $keys)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors($keys);
    }
}
