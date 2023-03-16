<?php

namespace Servidor\Projects\Actions;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Servidor\Projects\ProgressStep;
use Servidor\Projects\Project;
use Servidor\Projects\ProjectService;

class SaveSslCertificate
{
    /**
     * @phan-var Collection<mixed>
     *
     * @psalm-var Collection<array-key, mixed>
     */
    private Collection $config;

    public function __construct(private ProjectService $service)
    {
        $config = $service->config;

        if (
            null === $config
            || !$config->get('ssl', false)
            || !$service->domain_name
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
        $this->createCertsDir($this->service->project);

        \assert($this->service->config instanceof Collection);
        $this->service->config['sslCertificate'] = $this->saveCert($this->config);
        $this->service->config['sslPrivateKey'] = $this->saveKey($this->config);
    }

    private function createCertsDir(Project $project): bool
    {
        $path = storage_path('certs/' . Str::slug($project->name));

        return mkdir($path, 02750, true);
    }

    /**
     * @phan-param Collection<mixed> $config
     *
     * @psalm-param Collection<array-key, mixed> $config
     */
    private function saveCert(Collection $config): string
    {
        $cert = (string) $config->get('sslCertificate');

        return $this->saveFile($cert, 'crt');
    }

    /**
     * @phan-param Collection<mixed> $config
     *
     * @psalm-param Collection<array-key, mixed> $config
     */
    private function saveKey(Collection $config): string
    {
        $key = (string) $config->get('sslPrivateKey');

        return $this->saveFile($key, 'key');
    }

    private function saveFile(string $contents, string $extension): string
    {
        $projectName = $this->service->project->name;
        $file = $this->service->domain_name . '.' . $extension;
        $path = storage_path('certs/' . Str::slug($projectName) . '/' . $file);

        file_put_contents($path, $contents);

        return $path;
    }
}
