<?php

namespace Tests\Unit\Databases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

class DummyConnection extends Connection
{
    private AbstractSchemaManager $manager;

    public function __construct()
    {
        $this->manager = new class extends AbstractSchemaManager {
            private array $databases = ['information_schema', 'mysql', 'performance_schema'];

            public function __construct()
            {
            }

            public function createDatabase($database): void
            {
                $this->databases[] = $database;
            }

            public function listDatabases(): array
            {
                return $this->databases;
            }

            protected function _getPortableTableColumnDefinition($tableColumn): ?Column
            {
                return null;
            }
        };
    }

    public function getSchemaManager()
    {
        return $this->manager;
    }
}
