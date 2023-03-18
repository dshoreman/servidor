<?php

namespace Servidor\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Contracts\Config\Repository;
use Spatie\LaravelData\DataCollection;

class DatabaseManager
{
    public const DB_CREATE_EXISTS = 1007;

    private Connection $connection;

    /** @var AbstractSchemaManager<AbstractPlatform> */
    private AbstractSchemaManager $manager;

    /** @param AbstractSchemaManager<AbstractPlatform>|null $manager */
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

    public function create(DatabaseData $database): DatabaseData
    {
        try {
            $this->manager->createDatabase($database->name);
        } catch (DriverException $e) {
            if (self::DB_CREATE_EXISTS !== $e->getCode()) {
                throw $e;
            }
        }

        $data = $this->databases()->where('name', '=', $database->name)->first();
        \assert($data instanceof DatabaseData);

        return $data;
    }

    /**
     * @phpstan-return DataCollection<int, DatabaseData>
     *
     * @psalm-return DataCollection<array-key, mixed>
     */
    public function databases(): DataCollection
    {
        $databases = DatabaseData::collection($this->manager->listDatabases());

        \assert($databases instanceof DataCollection);

        return $databases;
    }

    /**
     * @phpstan-return DataCollection<int, DatabaseData>
     *
     * @psalm-return DataCollection<array-key, mixed>
     */
    public function detailedDatabases(): DataCollection
    {
        $databases = [];
        $sql = self::databasesSql();

        foreach ($this->connection->fetchAllAssociativeIndexed($sql) as $name => $result) {
            $databases[] = DatabaseData::from([
                'name' => (string) $name,
                'charset' => (string) $result['charset'],
                'collation' => (string) $result['collation'],
                'tableCount' => (int) $result['tableCount'],
            ]);
        }

        $collection = DatabaseData::collection($databases);
        \assert($collection instanceof DataCollection);

        return $collection;
    }

    public function databaseWithTables(DatabaseData|string $db): DatabaseData
    {
        $db = $db instanceof DatabaseData ? $db : DatabaseData::from($db);

        return $db->withTables(array_map(static function (array $result): TableData {
            /**
             * @var array{ TABLE_NAME: string,
             *             TABLE_COLLATION: string,
             *             TABLE_ROWS: int,
             *             DATA_LENGTH: int,
             *             ENGINE: string,
             *             } $result
             */

            return TableData::fromInfoSchema($result);
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
