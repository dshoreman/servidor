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
            $this->user = User::factory()->create();
        }

        return $this->actingAs($this->user, 'api');
    }
}
