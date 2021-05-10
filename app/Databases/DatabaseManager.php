<?php

namespace Servidor\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Contracts\Config\Repository;

class DatabaseManager
{
    private ?Connection $connection = null;

    private function connection(): Connection
    {
        if (isset($this->connection)) {
            return $this->connection;
        }

        $config = config();
        assert($config instanceof Repository);
        $socket = (string) $config->get('database.connections.mysql.unix_socket');

        $this->connection = DriverManager::getConnection(
            array_merge((array) config('database.dbal'), [
                'driver' => 'pdo_mysql',
                'unix_socket' => $socket,
            ]),
        );

        return $this->connection;
    }

    public function dbal(): AbstractSchemaManager
    {
        return $this->connection()->getSchemaManager();
    }

    public function hasDatabase(string $name): bool
    {
        $matches = array_filter(
            $this->listDatabases(),
            static fn (array $database): bool => $name === $database['name'],
        );

        return 0 < count($matches);
    }

    /** @return array<array{name: string}> */
    public function listDatabases(): array
    {
        $databases = array_map(
            static fn (string $name): array => ['name' => $name],
            $this->dbal()->listDatabases(),
        );

        return $databases;
    }

    public function create(string $name): bool
    {
        if ($this->hasDatabase($name)) {
            return true;
        }

        $this->dbal()->createDatabase($name);

        return $this->hasDatabase($name);
    }
}
