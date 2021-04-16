<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $endpoint = '/api';

    protected function assertValidationErrors($response, $keys): void
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors($keys);
    }

    protected function endpoint($id = null): string
    {
        $endpoint = $this->endpoint;

        if (str_contains($endpoint, '{id}')) {
            $endpoint = str_replace('{id}', $id, $endpoint);
        } elseif (is_numeric($id)) {
            $endpoint .= '/' . $id;
        } elseif (is_array($id) && count($id) > 0) {
            $parts = [];

            foreach ($id as $param => $value) {
                $parts[] = $param . '=' . $value;
            }

            $endpoint .= '?' . implode('&', $parts);
        }

        return $endpoint;
    }
}
