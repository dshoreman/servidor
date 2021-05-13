<?php

namespace Servidor\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Contracts\Config\Repository;

class DatabaseManager
{
    public const DB_CREATE_EXISTS = 1007;

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

    public function listDatabases(): DatabaseCollection
    {
        $databases = $this->dbal()->listDatabases();

        return DatabaseCollection::fromNames($databases);
    }

    public function create(Database $database): Database
    {
        try {
            $this->dbal()->createDatabase($database->name);
        } catch (DriverException $e) {
            if (self::DB_CREATE_EXISTS !== $e->getErrorCode()) {
                throw $e;
            }
        }

        return $this->listDatabases()->firstWhere('name', $database->name);
    }
}
