<?php

namespace Servidor\Listeners;

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
    }
}
