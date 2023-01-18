<?php

namespace Servidor\Projects\Applications;

use Exception;
use Servidor\Projects\Actions\SaveSslCertificate;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class PrepareSsl
{
    public function handle(ProjectAppSaving $event): void
    {
        $project = $event->getProject();

        $step = new ProgressStep('nginx.ssl', 'Saving SSL certificate', 40);
        ProjectProgress::dispatch($project, $step->start());

        try {
            (new SaveSslCertificate($event->getApp()))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (Exception $e) {
            ProjectProgress::dispatch($project, $step->skip($e->getMessage()));
        }
    }
}
