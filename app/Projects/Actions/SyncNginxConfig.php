<?php

namespace Servidor\Projects\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Factory as ViewFactory;
use Servidor\Projects\Application;
use Servidor\Projects\Redirect;

class SyncNginxConfig
{
    private string $configFile;

    public function __construct(
        private Application|Redirect $appOrRedirect,
    ) {
        $this->configFile = $appOrRedirect->domain_name . '.conf';
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
        $type = $this->appOrRedirect instanceof Application ? 'app' : 'redirect';
        $view = app(ViewFactory::class)->make($this->template());

        return $view->with($type, $this->appOrRedirect);
    }

    private function template(): string
    {
        $template = $this->appOrRedirect instanceof Redirect ? 'redirect'
            : $this->appOrRedirect->template()->nginxTemplate();

        return 'projects.app-templates.' . $template;
    }
}
