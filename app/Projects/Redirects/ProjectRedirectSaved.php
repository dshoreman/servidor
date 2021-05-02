<?php

namespace Servidor\Projects\Redirects;

use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class ProjectRedirectSaved
{
    use SerializesModels;

    public Redirect $redirect;

    public Project $project;

    public function __construct(Redirect $redirect)
    {
        assert($redirect->project instanceof Project);

        $this->redirect = $redirect;
        $this->project = $redirect->project;
    }
}
