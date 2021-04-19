<?php

namespace Servidor;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Contracts\Config\Repository;

class Database
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

    /** @return string[] */
    public function listDatabases(): array
    {
        return $this->dbal()->listDatabases();
    }

    public function create(string $dbname): bool
    {
        if (in_array($dbname, $this->listDatabases(), true)) {
            return true;
        }

        $this->dbal()->createDatabase($dbname);

        return in_array($dbname, $this->listDatabases(), true);
    }
}
