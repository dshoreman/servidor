<?php

namespace Servidor\Projects\Redirects;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Redirect;

class ProjectRedirectSaved
{
    use SerializesModels;

    public Redirect $redirect;

    public function __construct(Redirect $redirect)
    {
        $this->redirect = $redirect;
    }
}
