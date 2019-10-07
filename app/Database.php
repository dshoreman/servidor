<?php

namespace Servidor;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\MysqlSchemaManager;

class Database
{
    /**
     * @var Connection
     */
    private $connection;

    public function dbal(): MysqlSchemaManager
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection->getSchemaManager();
    }

    private function connect(): Connection
    {
        if (!$this->connection) {
            $this->connection = DriverManager::getConnection([
                'user' => config('database.dbal.user'),
                'password' => config('database.dbal.password'),
                'host' => config('database.connections.mysql.host'),
                'driver' => 'pdo_mysql',
            ], new Configuration());
        }

        return $this->connection;
    }

    public function listDatabases(): array
    {
        return $this->dbal()->listDatabases();
    }
}
