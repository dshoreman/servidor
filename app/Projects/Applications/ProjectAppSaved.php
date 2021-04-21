<?php

namespace Servidor\Projects\Applications;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Application;

class ProjectAppSaved
{
    use SerializesModels;

    public Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
