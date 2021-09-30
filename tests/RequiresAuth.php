<?php

namespace Tests;

use Servidor\User;

trait RequiresAuth
{
    /**
     * @var User
     */
    private $user;

    protected function authed()
    {
        if (!isset($this->user)) {
            $user = User::factory()->create();
            \assert($user instanceof User);
            $this->user = $user;
        }

        return $this->actingAs($this->user, 'api');
    }
}
