<?php

namespace Servidor\Projects;

use Illuminate\Support\Collection;
use Servidor\Projects\Actions\MissingProjectData;

/**
 * @property array<string, string> $requiredNginxData
 */
trait RequiresNginxData
{
    public function checkNginxData(): void
    {
        foreach ($this->requiredNginxData as $property => $name) {
            if ($this->checkConfig($property)) {
                continue;
            }

            throw new MissingProjectData(
                sprintf('Service does not have a %s set.', $name),
            );
        }
    }

    /** @phan-suppress PhanUndeclaredMethod, PhanUndeclaredProperty */
    private function checkConfig(string $property): bool
    {
        $data = $this->getService();

        if ('config.' !== mb_substr($property, 0, 7)) {
            return isset($data->{$property});
        }

        $config = $data->config ?: new Collection();
        $property = mb_substr($property, 7);

        if (str_contains($property, '.')) {
            [$key, $property] = explode('.', $property);

            /**
             * @var array<string, mixed> $key
             *
             * @psalm-suppress InvalidArgument
             * */
            $key = $config->get($key) ?: [];

            return isset($key[$property]);
        }

        return \array_key_exists($property, $config->toArray());
    }
}
