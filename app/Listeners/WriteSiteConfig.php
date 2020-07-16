<?php

namespace Servidor\Listeners;

use Illuminate\Support\Facades\Storage;
use Servidor\Events\SiteUpdated;

class WriteSiteConfig
{
    /**
     * @var \Servidor\Site
     */
    private $site;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string
     */
    private $symlink;

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
        $this->pullSite();

        $site->is_enabled
            ? $this->createSymlink()
            : $this->removeSymlink();

        exec('sudo systemctl reload-or-restart nginx.service');
    }

    private function updateConfig(): void
    {
        /** @var \Illuminate\View\View */
        $view = 'laravel' == $this->site->type
            ? view('sites.server-templates.php')
            : view('sites.server-templates.' . $this->site->type);

        Storage::put('vhosts/' . $this->filename, (string) $view->with('site', $this->site));

        /** @var \Illuminate\Filesystem\FilesystemAdapter */
        $disk = Storage::disk('local');
        $file = $disk->path('vhosts/' . $this->filename);
        exec('sudo cp "' . $file . '" "' . $this->configPath . '"');
    }

    private function pullSite(): void
    {
        $root = $this->site->document_root;
        $branch = $this->site->source_branch;

        if (!$this->site->type || 'redirect' == $this->site->type || !$root) {
            return;
        }

        if (is_dir($root . '/.git')) {
            exec('cd "' . $root . '"' . ($branch ? ' && git checkout "' . $branch . '"' : '') . ' && git pull');

            return;
        }

        if (!is_dir(dirname($root))) {
            mkdir(dirname($root), 755);
        }

        $cloneCmd = $branch ? 'git clone --branch "' . $branch . '"' : 'git clone';
        exec($cloneCmd . ' "' . $this->site->source_repo . '" "' . $root . '"');
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
