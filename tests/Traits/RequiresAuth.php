<?php namespace Tests\Traits;

use Servidor\User;

trait RequiresAuth
{
    /**
     * @var User
     */
    private $user;

    protected function authed()
    {
        if (!$this->user) {
            $this->user = factory(User::class)->create();
        }

        return $this->actingAs($this->user, 'api');
    }
}
