<?php

namespace Servidor;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class Database
{
    private ?Connection $connection = null;

    private function connection(): Connection
    {
        if (isset($this->connection)) {
            return $this->connection;
        }

        $this->connection = DriverManager::getConnection([
            'user' => config('database.dbal.user'),
            'password' => config('database.dbal.password'),
            'host' => config('database.connections.mysql.host'),
            'driver' => 'pdo_mysql',
        ]);

        return $this->connection;
    }

    public function dbal(): AbstractSchemaManager
    {
        return $this->connection()->getSchemaManager();
    }

    /** @return array<array-key, string> */
    public function listDatabases(): array
    {
        return $this->dbal()->listDatabases();
    }

    public function create(string $dbname): bool
    {
        if (in_array($dbname, $this->listDatabases())) {
            return true;
        }

        $this->dbal()->createDatabase($dbname);

        return in_array($dbname, $this->listDatabases());
    }
}
