<?php

namespace Tests\Unit\System\Users;

use PHPUnit\Framework\TestCase;
use Servidor\System\Users\LinuxUser;

class LinuxUserTest extends TestCase
{
    /** @test */
    public function disabling_toggle_arg_with_no_off_value_should_not_add_empty_element(): void
    {
        $user = new LinuxUser([
            'name' => 'foo',
        ]);

        $user->toggleArg(false, '--foo', '');

        $this->assertEquals([], $user->getArgs());
        $this->assertEquals('', $user->toArgs());
        $this->assertFalse($user->isDirty());
    }
}
