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

    /** @var array<string, int> */
    private array $tableCounts = [];

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
        $this->countTables();

        return $this->databases()->mapWithKeys(function (DatabaseData $database): array {
            $tableCount = $this->tableCounts[$database->name] ?? 0;

            return [$database->name => $database->withTableCount($tableCount)];
        });
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

        return new TableCollection(array_map(
            static fn (array $result): TableData => new TableData($result['TABLE_NAME']),
            $query->fetchAllAssociative(),
        ));
    }

    private function countTables(): void
    {
        $fields = 'dbName, COUNT(*) AS tableCount';
        $sql = 'SELECT TABLE_SCHEMA AS %s FROM information_schema.tables GROUP BY dbName';

        $countData = $this->connection->fetchAllKeyValue(sprintf($sql, $fields));

        array_walk($countData, function (int $tableCount, string $database): void {
            $this->tableCounts[$database] = $tableCount;
        });
    }
}
