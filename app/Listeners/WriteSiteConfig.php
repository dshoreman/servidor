<?php

namespace Servidor\Listeners;

use Illuminate\Support\Facades\Storage;
use Servidor\Events\SiteUpdated;

class WriteSiteConfig
{
    /**
     * Handle the event.
     *
     * @param SiteUpdated $event
     */
    public function handle(SiteUpdated $event)
    {
        $site = $event->site;

        if ($site->document_root && !is_dir($site->document_root)) {
            mkdir($site->document_root, 755);
        }

        $filename = $site->primary_domain.'.conf';
        $filepath = 'vhosts/'.$filename;
        $fullpath = Storage::disk('local')->path($filepath);
        $confpath = '/etc/nginx/sites-available/'.$filename;
        $livepath = '/etc/nginx/sites-enabled/'.$filename;

        $template = 'laravel' == $site->type ? 'php' : $site->type;

        Storage::put($filepath, view('sites.server-templates.'.$template, ['site' => $site]));
        exec('sudo cp "'.$fullpath.'" "'.$confpath.'"');

        if ($site->is_enabled) {
            $this->createSymlink($confpath, $livepath);
        } else {
            $this->removeSymlink($confpath, $livepath);
        }

        exec('sudo systemctl reload-or-restart nginx.service');
    }

    private function createSymlink(string $target, string $symlink)
    {
        if (is_link($symlink) && readlink($symlink) == $target) {
            return;
        }

        if (file_exists($symlink)) {
            exec('sudo rm "'.$symlink.'"');
        }

        exec('sudo ln -s "'.$target.'" "'.$symlink.'"');
    }

    private function removeSymlink(string $target, string $symlink)
    {
        if (!is_link($symlink) || readlink($symlink) != $target) {
            return;
        }

        exec('sudo rm "'.$symlink.'"');
    }
}
