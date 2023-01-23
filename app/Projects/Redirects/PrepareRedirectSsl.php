<?php

namespace Servidor\Projects\Redirects;

use Exception;
use Servidor\Projects\Actions\SaveSslCertificate;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class PrepareRedirectSsl
{
    public function handle(ProjectRedirectSaving $event): void
    {
        $project = $event->getProject();

        $step = new ProgressStep('nginx.ssl', 'Saving SSL certificate', 40);
        ProjectProgress::dispatch($project, $step->start());

        try {
            (new SaveSslCertificate($event->getRedirect()))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (Exception $e) {
            ProjectProgress::dispatch($project, $step->skip($e->getMessage()));
        }
    }
}
