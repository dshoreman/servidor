<?php

namespace Tests\Unit\Databases;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

/**
 * @extends AbstractSchemaManager<AbstractPlatform>
 */
class FakeSchemaManager extends AbstractSchemaManager
{
    /**
     * @var array<string>
     */
    private array $databases = ['information_schema', 'mysql', 'performance_schema', 'servidor_testing'];

    public function __construct()
    {
    }

    public function createDatabase($database): void
    {
        if (!\in_array($database, $this->databases, true)) {
            $this->databases[] = $database;
        }
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
