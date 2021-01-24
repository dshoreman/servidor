<?php

namespace Servidor\Traits;

use Exception;
use Servidor\Projects\Applications\Templates\Template;
use Servidor\Projects\Redirect;

trait TogglesNginxConfigs
{
    /** @var string */
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

    private function configAvailable(): string
    {
        return $this->nginxRoot . 'sites-available/' . $this->configFilename();
    }

    private function configEnabled(): string
    {
        return $this->nginxRoot . 'sites-enabled/' . $this->configFilename();
    }

    private function configFilename(): string
    {
        if ($this instanceof Template && $domain = $this->getApp()->domain_name) {
            return "${domain}.conf";
        }

        if ($this instanceof Redirect) {
            return $this->domain_name . '.conf';
        }

        throw new Exception('Project missing domain name');
    }
}
