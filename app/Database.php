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
                'user' => env('DB_ADMIN_USER'),
                'password' => env('DB_ADMIN_PASS'),
                'host' => 'localhost',
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
