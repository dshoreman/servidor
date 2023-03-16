<?php

namespace Servidor\Projects\Services\Templates;

use Servidor\Projects\ProjectService;
use Servidor\Projects\Services\LogFile;

interface Template
{
    public function getService(): ProjectService;

    /**
     * @return array<string, LogFile>
     */
    public function getLogs(): array;

    public function nginxTemplate(): string;

    public function checkNginxData(): void;

    public function publicDir(): string;

    public function requiresUser(): bool;

    public function serviceType(): string;
}
