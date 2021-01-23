<?php

namespace Servidor\Traits;

use Servidor\Projects\Redirect;

trait TogglesNginxConfigs
{
    private $nginxRoot = '/etc/nginx/';

    public function disable(): void
    {
        $symlink = $this->configEnabled();

        if (!is_link($symlink) || readlink($symlink) !== $this->configAvailable()) {
            return;
        }

        exec('sudo rm "' . $symlink . '"');
        clearstatcache(true, $symlink);
    }

    public function enable(): void
    {
        $symlink = $this->configEnabled();
        $target = $this->configAvailable();

        if (is_link($symlink) && readlink($symlink) === $target) {
            return;
        }

        if (file_exists($symlink)) {
            exec('sudo rm "' . $symlink . '"');
        }

        exec("sudo ln -s \"{$target}\" \"{$symlink}\"");
    }

    private function configAvailable()
    {
        return $this->nginxRoot . 'sites-available/' . $this->configFilename();
    }

    private function configEnabled()
    {
        return $this->nginxRoot . 'sites-enabled/' . $this->configFilename();
    }

    private function configFilename(): string
    {
        if ($this instanceof Redirect) {
            return $this->domain_name . '.conf';
        }

        return $this->app->domain_name . '.conf';
    }
}
