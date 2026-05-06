<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use Framework\Database\DatabaseInterface;
use Framework\Database\QueryBuilder;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    private $dbMock;
    private QueryBuilder $builder;

    protected function setUp(): void
    {
        // 1. Criamos um mock da interface para satisfazer o construtor
        $this->dbMock = $this->createMock(DatabaseInterface::class);

        // 2. Instanciamos passando o mock e uma tabela alvo
        $this->builder = new QueryBuilder($this->dbMock, 'users');
    }

    public function test_it_builds_a_simple_select_all(): void
    {
        // No seu código o método é toSql()
        $sql = $this->builder
            ->select('*')
            ->toSql();

        $this->assertEquals("SELECT * FROM users", $sql);
    }

    public function test_it_builds_select_with_where_conditions(): void
    {
        // O seu where(col, op, val) gera placeholders automáticos :col_0
        $sql = $this->builder
            ->select('id', 'username')
            ->where('active', '=', '1')
            ->toSql();

        $this->assertEquals("SELECT id, username FROM users WHERE active = :active_0", $sql);
    }


    public function test_it_builds_with_order_and_limit(): void
    {
        $sql = $this->builder
            ->select('name')
            ->orderByDesc('price')
            ->limit(10)
            ->toSql();

        // Ajustado de 'products' para 'users'
        $this->assertEquals("SELECT name FROM users ORDER BY price DESC LIMIT 10", $sql);
    }

    public function test_it_builds_with_joins(): void
    {
        $sql = $this->builder
            ->select('users.name', 'roles.title')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->toSql();

        $this->assertEquals(
            "SELECT users.name, roles.title FROM users INNER JOIN roles ON users.role_id = roles.id",
            $sql
        );
    }
}
