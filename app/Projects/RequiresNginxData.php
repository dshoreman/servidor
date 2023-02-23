<?php

namespace Servidor\Projects;

use Servidor\Projects\Actions\MissingProjectData;

/**
 * @property array<string, string> $requiredNginxData
 */
trait RequiresNginxData
{
    public function checkNginxData(): void
    {
        $type = $this instanceof Redirect ? 'Redirect' : 'App';

        foreach ($this->requiredNginxData as $property => $name) {
            if ($this->checkConfig($property)) {
                continue;
            }

            throw new MissingProjectData(
                sprintf('%s does not have a %s set.', $type, $name),
            );
        }
    }

    /** @phan-suppress PhanUndeclaredMethod, PhanUndeclaredProperty */
    private function checkConfig(string $property): bool
    {
        /**
         * @var Application|Redirect $data
         *
         * @phpstan-ignore-next-line
         */
        $data = method_exists(self::class, 'getApp') ? $this->getApp() : $this;

        if ('config.' !== mb_substr($property, 0, 7)) {
            return isset($data->{$property});
        }

        if (!$data->config) {
            return false;
        }

        $property = mb_substr($property, 7);

        if (str_contains($property, '.')) {
            [$key, $property] = explode('.', $property);

            /** @var array $key */
            $key = $data->config->get($key, []);

            return isset($key[$property]);
        }

        return $data->config->has($property);
    }
}
