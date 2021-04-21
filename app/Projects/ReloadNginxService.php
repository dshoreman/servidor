<?php

namespace Servidor\Projects;

class ReloadNginxService
{
    public function handle(): void
    {
        exec('sudo systemctl reload-or-restart nginx.service');
    }
}
