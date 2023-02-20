<?php

namespace Servidor\Projects;

use Servidor\Projects\Actions\MissingProjectData;
use Servidor\Projects\Applications\Templates\Template;

/**
 * @property array<string, string> $requiredNginxData
 */
trait RequiresNginxData
{
    public function checkNginxData(): void
    {
        $type = $this instanceof Redirect ? 'Redirect' : 'App';

        foreach ($this->requiredNginxData as $property => $name) {
            if ($this instanceof Template && $this->checkConfig($property)) {
                continue;
            }
            if ($this instanceof Redirect && $this->{$property}) {
                continue;
            }

            throw new MissingProjectData(
                sprintf('%s does not have a %s set.', $type, $name),
            );
        }
    }

    private function checkConfig(string $property): bool
    {
        \assert($this instanceof Template);
        if ('config.' !== mb_substr($property, 0, 7)) {
            return isset($this->getApp()->{$property});
        }

        $config = $this->getApp()->config;
        $property = mb_substr($property, 7);

        if ($config && str_contains($property, '.')) {
            [$key, $property] = explode('.', $property);

            /** @var array */
            $key = $config->get($key, []);

            return isset($key[$property]);
        }

        return $config && $config->has($property);
    }
}
