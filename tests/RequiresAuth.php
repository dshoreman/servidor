<?php

namespace Tests;

use Servidor\User;

trait RequiresAuth
{
    private ?User $user;

    protected function authed(): TestCase
    {
        if (!isset($this->user)) {
            $user = User::factory()->create();
            \assert($user instanceof User);
            $this->user = $user;
        }

        return $this->actingAs($this->user, 'api');
    }
}
