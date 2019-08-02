<?php

namespace Servidor\Events;

use Illuminate\Queue\SerializesModels;
use Servidor\Site;

class SiteUpdated
{
    use SerializesModels;

    /**
     * @var Site
     */
    public $site;

    /**
     * Create a new event instance.
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
}
