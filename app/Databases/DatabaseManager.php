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

    private Connection $connection;

    private AbstractSchemaManager $manager;

    public function __construct(
        Repository $config,
        ?Connection $connection = null,
        ?AbstractSchemaManager $manager = null,
    ) {
        /** @var array{user: string, password: string} $dbalConfig */
        $dbalConfig = $config->get('database.dbal');
        $socket = (string) $config->get('database.connections.mysql.unix_socket');

        /** @psalm-suppress InvalidArgument */
        $this->connection = $connection ?? DriverManager::getConnection(
            array_merge($dbalConfig, [
                'driver' => 'pdo_mysql',
                'unix_socket' => $socket,
            ]),
        );
        $this->manager = $manager ?: $this->connection->createSchemaManager();
    }

    public function create(DatabaseDTO $database): DatabaseDTO
    {
        try {
            $this->manager->createDatabase($database->name);
        } catch (DriverException $e) {
            if (self::DB_CREATE_EXISTS !== $e->getCode()) {
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
        $sql = self::databasesSql();

        foreach ($this->connection->fetchAllAssociativeIndexed($sql) as $name => $result) {
            $databases[$name] = new DatabaseDTO(
                name: (string) $name,
                charset: (string) $result['charset'],
                collation: (string) $result['collation'],
                tableCount: (int) $result['tableCount'],
            );
        }

        return new DatabaseCollection($databases);
    }

    public function databaseWithTables(DatabaseDTO|string $db): DatabaseDTO
    {
        $db = $db instanceof DatabaseDTO ? $db : new DatabaseDTO($db);

        return $db->withTables(array_map(static function (array $result): TableDTO {
            /**
             * @var array{ TABLE_NAME: string,
             *             TABLE_COLLATION: string,
             *             TABLE_ROWS: int,
             *             DATA_LENGTH: int,
             *             ENGINE: string,
             *             } $result
             */

            return TableDTO::fromInfoSchema($result);
        }, $this->connection->fetchAllAssociative(
            self::tablesSql(),
            ['db' => $db->name],
        )));
    }

    public static function databasesSql(): string
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

    public static function tablesSql(): string
    {
        return <<<'endQuery'
                SELECT TABLE_NAME, ENGINE, TABLE_ROWS, DATA_LENGTH, TABLE_COLLATION
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = :db
            endQuery;
    }
}
