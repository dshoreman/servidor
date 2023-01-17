<?php

namespace Servidor\Projects\Applications;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\ProjectProgress;

class SaveSslCertificate
{
    private string $projectName = '';

    private string $domainName = '';

    public function handle(ProjectAppSaving $event): void
    {
        $app = $event->getApp();
        $project = $event->getProject();
        $config = $app->config;

        $step = new ProgressStep('nginx.ssl', 'Saving SSL certificate', 40);
        ProjectProgress::dispatch($project, $step);
        $reason = $this->shouldPreventCreation($config);

        if (\is_string($reason)) {
            ProjectProgress::dispatch($project, $step->skip($reason));

            return;
        }

        $this->projectName = $event->getProject()->name;
        $this->domainName = $event->getApp()->domain_name;

        \assert($app->config instanceof Collection && $config instanceof Collection);
        $app->config['sslCertificate'] = $this->saveCert($config);
        $app->config['sslPrivateKey'] = $this->saveKey($config);

        ProjectProgress::dispatch($project, $step->complete());
    }

    private function saveCert(Collection $config): string
    {
        $cert = (string) $config->get('sslCertificate');

        return $this->saveFile($cert, 'crt');
    }

    private function saveKey(Collection $config): string
    {
        $key = (string) $config->get('sslPrivateKey');

        return $this->saveFile($key, 'key');
    }

    private function saveFile(string $contents, string $extension): string
    {
        $file = $this->domainName . '.' . $extension;
        $path = storage_path('certs/' . Str::slug($this->projectName) . '/' . $file);

        file_put_contents($path, $contents);

        return $path;
    }

    private function shouldPreventCreation(?Collection $config): bool|string
    {
        if (null === $config || !$config->get('ssl', false) || !$this->domainName) {
            return ProgressStep::REASON_REQUIRED;
        }

        if (!$config->get('sslCertificate') || !$config->get('sslPrivateKey')) {
            return ProgressStep::REASON_MISSING_DATA;
        }

        return false;
    }
}
