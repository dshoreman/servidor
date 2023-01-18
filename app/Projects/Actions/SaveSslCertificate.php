<?php

namespace Servidor\Projects\Actions;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Servidor\Projects\Application;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\Project;
use Servidor\Projects\Redirect;

class SaveSslCertificate
{
    private Collection $config;

    public function __construct(
        private Application|Redirect $appOrRedirect,
    ) {
        $config = $appOrRedirect->config;

        if (
            null === $config
            || !$config->get('ssl', false)
            || !$appOrRedirect->domain_name
        ) {
            throw new Exception(ProgressStep::REASON_REQUIRED);
        }

        if (!$config->get('sslCertificate') || !$config->get('sslPrivateKey')) {
            throw new Exception(ProgressStep::REASON_MISSING_DATA);
        }

        $this->config = $config;
    }

    public function execute(): void
    {
        \assert($this->appOrRedirect->project instanceof Project);
        $this->createCertsDir($this->appOrRedirect->project);

        \assert($this->appOrRedirect->config instanceof Collection);
        $this->appOrRedirect->config['sslCertificate'] = $this->saveCert($this->config);
        $this->appOrRedirect->config['sslPrivateKey'] = $this->saveKey($this->config);
    }

    private function createCertsDir(Project $project): bool
    {
        $path = storage_path('certs/' . Str::slug($project->name));

        return mkdir($path, 02750, true);
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
        \assert($this->appOrRedirect->project instanceof Project);
        $projectName = $this->appOrRedirect->project->name;
        $file = $this->appOrRedirect->domain_name . '.' . $extension;
        $path = storage_path('certs/' . Str::slug($projectName) . '/' . $file);

        file_put_contents($path, $contents);

        return $path;
    }
}
