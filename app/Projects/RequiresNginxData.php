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
            if ($this instanceof Template && $this->getApp()->{$property}) {
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
}
