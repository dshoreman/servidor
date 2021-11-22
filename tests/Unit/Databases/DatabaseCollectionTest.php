<?php

namespace Tests\Unit\Databases;

use PHPUnit\Framework\TestCase;
use Servidor\Databases\DatabaseCollection;
use Servidor\Databases\DatabaseDTO;

class DatabaseCollectionTest extends TestCase
{
    /** @test */
    public function get_returns_default_if_not_found(): void
    {
        $collection = new DatabaseCollection();
        $default = new DatabaseDTO(name: 'NOMATCH');

        $this->assertInstanceOf(DatabaseCollection::class, $collection);
        $this->assertEquals('NOMATCH', $collection->get('c', $default)->name);
    }

    /** @test */
    public function get_throws_exception_with_no_default(): void
    {
        $collection = new DatabaseCollection();

        $this->expectExceptionMessage('cake does not exist');

        $collection->get('cake');
    }
}
