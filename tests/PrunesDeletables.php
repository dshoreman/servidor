<?php

namespace Tests;

trait PrunesDeletables
{
    /** @var string[] */
    private array $deletableGroups = [];

    /** @var array<string, bool> */
    private array $deletableUsers = [];

    private function addDeletableGroup(string $group): void
    {
        $this->deletableGroups[] = $group;
    }

    private function addDeletableUser(string $user, bool $purgeHome = false): void
    {
        $this->deletableUsers[$user] = $purgeHome;
    }

    private function pruneDeletableGroups(): void
    {
        foreach ($this->deletableGroups as $group) {
            exec('sudo groupdel ' . escapeshellarg($group));
        }
    }

    private function pruneDeletableUsers(): void
    {
        foreach ($this->deletableUsers as $user => $prune) {
            $user = escapeshellarg($user);
            $command = 'sudo id ' . $user . ' &>/dev/null && sudo userdel ' . $user;

            if ($prune) {
                $command .= ' --remove --force';
            }

            exec($command);
        }
    }
}
