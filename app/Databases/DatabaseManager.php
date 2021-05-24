<?php

namespace Servidor\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Illuminate\Contracts\Config\Repository;

class DatabaseManager
{
    public const DB_CREATE_EXISTS = 1007;

    private Connection $connection;

    private AbstractSchemaManager $manager;

    public function __construct(Repository $config, ?AbstractSchemaManager $manager = null)
    {
        $socket = (string) $config->get('database.connections.mysql.unix_socket');

        $this->connection = DriverManager::getConnection(
            array_merge((array) $config->get('database.dbal'), [
                'driver' => 'pdo_mysql',
                'unix_socket' => $socket,
            ]),
        );
        $this->manager = $manager ?: new MySqlSchemaManager($this->connection);
    }

    public function create(DatabaseData $database): DatabaseData
    {
        try {
            $this->manager->createDatabase($database->name);
        } catch (DriverException $e) {
            if (self::DB_CREATE_EXISTS !== $e->getErrorCode()) {
                throw $e;
            }
        }

        return $this->databases()->get($database->name);
    }

    public function databases(): DatabaseCollection
    {
        $databases = $this->manager->listDatabases();

        return DatabaseCollection::fromNames($databases);
    }

    public function detailedDatabases(): DatabaseCollection
    {
        $databases = [];
        $sql = $this->detailedDatabasesSql();

        foreach ($this->connection->fetchAllAssociativeIndexed($sql) as $name => $result) {
            $databases[$name] = new DatabaseData(
                (string) $name,
                null,
                (int) $result['tableCount'],
                (string) $result['charset'],
                (string) $result['collation'],
            );
        }

        return new DatabaseCollection($databases);
    }

    public function tables(DatabaseData $database): TableCollection
    {
        $builder = $this->connection->createQueryBuilder();

        $builder
            ->select(['TABLE_NAME', 'ENGINE', 'TABLE_ROWS', 'DATA_LENGTH', 'TABLE_COLLATION'])
            ->from('information_schema.TABLES')
            ->where('TABLE_SCHEMA = :db')
            ->setParameter('db', $database->name)
        ;
        $query = $builder->execute();
        \assert($query instanceof DriverStatement);

        return new TableCollection(array_map(static function (array $result): TableData {
            /**
             * @var array{ TABLE_NAME: string,
             *             TABLE_COLLATION: string,
             *             TABLE_ROWS: int,
             *             DATA_LENGTH: int,
             *             ENGINE: string,
             *             } $result
             */

            return TableData::fromInfoSchema($result);
        }, $query->fetchAllAssociative()));
    }

    private function detailedDatabasesSql(): string
    {
        return <<<'endQuery'
                SELECT
                    db.SCHEMA_NAME AS name,
                    db.DEFAULT_COLLATION_NAME AS collation,
                    db.DEFAULT_CHARACTER_SET_NAME AS charset,
                    COUNT(tbl.TABLE_SCHEMA) AS tableCount
                FROM information_schema.SCHEMATA AS db
                LEFT JOIN information_schema.TABLES tbl
                    ON db.SCHEMA_NAME = tbl.TABLE_SCHEMA
                GROUP BY name, charset, collation
            endQuery;
    }
}
