<?php

namespace Servidor\Projects\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Factory as ViewFactory;
use Servidor\Projects\ProjectService;

class SyncNginxConfig
{
    private string $configFile;

    public function __construct(
        private ProjectService $service,
    ) {
        $service->checkNginxData();

        $this->configFile = $service->domain_name . '.conf';
    }

    public function execute(): void
    {
        $local = 'vhosts/' . $this->configFile;

        Storage::put($local, $this->fileContents()->render());

        exec(sprintf(
            'sudo cp "%s" "%s"',
            storage_path('app/' . $local),
            '/etc/nginx/sites-available/' . $this->configFile,
        ));
    }

    private function fileContents(): View
    {
        $view = app(ViewFactory::class)->make($this->template());

        return $view->with('service', $this->service);
    }

    private function template(): string
    {
        $template = $this->service->template()->nginxTemplate();

        return 'projects.templates.' . $template;
    }
}
