<?php

namespace Servidor\Projects\Services;

use Exception;
use Servidor\Projects\Actions\SaveSslCertificate;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class PrepareSsl
{
    public function handle(ProjectServiceSaving $event): void
    {
        $project = $event->getProject();
        $progress = 'redirect' === $event->getService()->getType() ? 40 : 20;

        $step = new ProgressStep('nginx.ssl', 'Saving SSL certificate', $progress);
        ProjectProgress::dispatch($project, $step->start());

        try {
            (new SaveSslCertificate($event->getService()))->execute();

            ProjectProgress::dispatch($project, $step->complete());
        } catch (Exception $e) {
            ProjectProgress::dispatch($project, $step->skip($e->getMessage()));
        }
    }
}
