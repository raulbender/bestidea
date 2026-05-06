<?php

declare(strict_types=1);

namespace Tests\Integration;

use Framework\Container;
use Framework\Database\DatabaseInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private DatabaseInterface $db;

    protected function setUp(): void
    {
        $this->db = Container::resolve(DatabaseInterface::class);
    }

    #[Test]
    public function test_if_database_is_resolved_as_singleton(): void
    {
        $db1 = Container::resolve(DatabaseInterface::class);
        $db2 = Container::resolve(DatabaseInterface::class);

        $this->assertSame($db1, $db2, "The container should return the same instance.");
    }

    #[Test]
    public function test_query_execution_returns_expected_array(): void
    {
        $row = $this->db->fetchAssoc("SELECT 1 + 1 AS result");
        $this->assertEquals(2, $row['result']);
    }

    #[Test]
    public function test_query_as_class_mapping_logic(): void
    {
        $sql = "SELECT 'Naval Steel' AS material";
        $stmt = $this->db->queryAsClass($sql);
        $result = $stmt->fetch();

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertEquals('Naval Steel', $result->material);
    }

    #[Test]
    public function test_adapter_last_insert_id_returns_string(): void
    {
        $this->assertIsString($this->db->lastInsertId());
    }
}
