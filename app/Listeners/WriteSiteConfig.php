<?php

namespace Servidor\Listeners;

use Illuminate\Support\Facades\Storage;
use Servidor\Events\SiteUpdated;
use Servidor\Site;

class WriteSiteConfig
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var string
     */
    private $filename = '';

    /**
     * @var string
     */
    private $configPath = '';

    /**
     * @var string
     */
    private $symlink = '';

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Handle the event.
     */
    public function handle(SiteUpdated $event): void
    {
        if (!is_dir('/etc/nginx/sites-available')) {
            return;
        }

        $site = $this->site = $event->site;
        $filename = $this->filename = $site->primary_domain . '.conf';
        $this->configPath = '/etc/nginx/sites-available/' . $filename;
        $this->symlink = '/etc/nginx/sites-enabled/' . $filename;

        $this->updateConfig();

        $site->is_enabled
            ? $this->createSymlink()
            : $this->removeSymlink();

        exec('sudo systemctl reload-or-restart nginx.service');
    }

    private function updateConfig(): void
    {
        $type = $this->site->type ?? 'basic';
        /** @var \Illuminate\View\View */
        $view = 'laravel' == $type
            ? view('sites.server-templates.php')
            : view('sites.server-templates.' . $type);

        Storage::put('vhosts/' . $this->filename, (string) $view->with('site', $this->site));

        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $disk = Storage::disk('local');
        $file = $disk->path('vhosts/' . $this->filename);
        exec('sudo cp "' . $file . '" "' . $this->configPath . '"');
    }

    private function createSymlink(): void
    {
        if (is_link($symlink = $this->symlink) && readlink($symlink) == $this->configPath) {
            return;
        }

        if (file_exists($symlink)) {
            exec('sudo rm "' . $symlink . '"');
        }

        exec('sudo ln -s "' . $this->configPath . '" "' . $symlink . '"');
    }

    private function removeSymlink(): void
    {
        if (!is_link($symlink = $this->symlink) || readlink($symlink) != $this->configPath) {
            return;
        }

        exec('sudo rm "' . $symlink . '"');
    }
}
