<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

class VersionTest extends TestCase
{
    /** @test */
    public function version_command_outputs_current_servidor_version(): void
    {
        $cmd = $this->artisan('servidor:version');

        $cmd->expectsOutput('Servidor v' . SERVIDOR_VERSION);
        $cmd->assertExitCode(0);
    }
}
