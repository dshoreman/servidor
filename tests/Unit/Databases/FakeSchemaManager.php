<?php

namespace Tests\Unit\Databases;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

class FakeSchemaManager extends AbstractSchemaManager
{
    private array $databases = ['information_schema', 'mysql', 'performance_schema', 'servidor_testing'];

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
}
