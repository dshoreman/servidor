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

    public function hasDatabase(Database $database): bool
    {
        $matches = array_filter(
            $this->listDatabases(),
            static fn (Database $result): bool => $database->name === $result->name,
        );

        return 0 < count($matches);
    }

    /** @return array<Database> */
    public function listDatabases(): array
    {
        $databases = array_map(
            static fn (string $name): Database => new Database($name),
            $this->dbal()->listDatabases(),
        );

        return $databases;
    }

    public function create(Database $database): bool
    {
        if ($this->hasDatabase($database)) {
            return true;
        }

        $this->dbal()->createDatabase($database->name);

        return $this->hasDatabase($database);
    }
}
