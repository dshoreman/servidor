<?php

namespace Servidor\Listeners;

use Illuminate\Support\Facades\Storage;
use Servidor\Events\SiteUpdated;

class WriteSiteConfig
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $config_path;

    /**
     * @var string
     */
    private $symlink;

    /**
     * Handle the event.
     *
     * @param SiteUpdated $event
     */
    public function handle(SiteUpdated $event)
    {
        $site = $this->site = $event->site;

        if ($site->document_root && !is_dir($site->document_root)) {
            mkdir($site->document_root, 755);
        }

        $filename = $this->filename = $site->primary_domain.'.conf';
        $this->config_path = '/etc/nginx/sites-available/'.$filename;
        $this->symlink = '/etc/nginx/sites-enabled/'.$filename;

        $this->updateConfig();

        $site->is_enabled
            ? $this->createSymlink()
            : $this->removeSymlink();

        exec('sudo systemctl reload-or-restart nginx.service');
    }

    private function updateConfig()
    {
        $view = 'laravel' == $this->site->type
            ? view('sites.server-templates.php')
            : view('sites.server-templates.'.$this->site->type);

        Storage::put('vhosts/'.$this->filename, $view->with('site', $this->site));

        $file = Storage::disk('local')->path('vhosts/'.$this->filename);
        exec('sudo cp "'.$file.'" "'.$this->config_path.'"');
    }

    private function createSymlink()
    {
        if (is_link($symlink = $this->symlink) && readlink($symlink) == $this->config_path) {
            return;
        }

        if (file_exists($symlink)) {
            exec('sudo rm "'.$symlink.'"');
        }

        exec('sudo ln -s "'.$this->config_path.'" "'.$symlink.'"');
    }

    private function removeSymlink()
    {
        if (!is_link($symlink = $this->symlink) || readlink($symlink) != $this->config_path) {
            return;
        }

        exec('sudo rm "'.$symlink.'"');
    }
}
