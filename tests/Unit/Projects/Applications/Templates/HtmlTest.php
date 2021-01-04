<?php

namespace Tests\Unit\Projects\Applications\Templates;

use Servidor\Projects\Application;
use Tests\TestCase;

class HtmlTest extends TestCase
{
    /** @test */
    public function getLogs_returns_empty_array(): void
    {
        $app = new Application(['template' => 'html']);

        $this->assertEmpty($app->template()->getLogs());
    }
}
