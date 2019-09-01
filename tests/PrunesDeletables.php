<?php

namespace Tests;

trait PrunesDeletables
{
    private $deletables = [
        'groups' => [
            'items' => [],
            'key' => 'gid',
        ],
        'users' => [
            'items' => [],
            'key' => 'uid',
        ],
    ];

    private function addDeletable($type, $data)
    {
        $key = mb_strtolower($type).'s';

        if (!array_key_exists($key, $this->deletables)) {
            throw new Exception('Invalid deletable type "'.$type.'".');
        }

        $filter = $this->deletables[$key]['key'];

        $this->deletables[$key]['items'][] = $data->json()[$filter];
    }

    private function pruneDeletable($types = [])
    {
        if (!is_array($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
            $endpoint = "/api/system/{$type}/";

            foreach ($this->deletables[$type]['items'] as $id) {
                $this->authed()->deleteJson($endpoint.$id, []);
            }
        }
    }
}
