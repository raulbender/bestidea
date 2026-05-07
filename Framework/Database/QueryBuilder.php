<?php

declare(strict_types=1);

namespace Framework\Database;

class QueryBuilder {
    /** @var array<int, string> */
    private array $selects = ['*'];

    /** @var array<int, string> */
    private array $wheres = [];

    /** @var array<int, string> */
    private array $joins = [];

    /** @var array<int, string> */
    private array $orders = [];

    private ?int $limit = null;
    private ?int $offset = null;

    /** @var array<string, mixed> */
    private array $bindValues = [];

    public function __construct(
        private DatabaseInterface $db,
        private string $table
    ) {
    }

    public function select(string ...$columns): self {

        $this->selects = array_values($columns);

        return $this;
    }

    public function where(string $column, string $operator, mixed $value = null): self {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = str_replace('.', '_', $column) . "_" . count($this->wheres);

        $this->wheres[] = "{$column} {$operator} :{$placeholder}";
        $this->bindValues[$placeholder] = $value;

        return $this;
    }

    public function whereIn(string $column, array $values): self {
        if (empty($values)) {
            $this->wheres[] = "1 = 0"; // Garante que não retorne nada se o array for vazio
            return $this;
        }

        $placeholders = [];
        foreach ($values as $index => $value) {
            $placeholder = str_replace('.', '_', $column) . "_in_" . count($this->bindValues);
            $placeholders[] = ":{$placeholder}";
            $this->bindValues[$placeholder] = $value;
        }

        $this->wheres[] = "{$column} IN (" . implode(', ', $placeholders) . ")";

        return $this;
    }

    public function limit(int $limit): self {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self {
        $this->offset = $offset;

        return $this;
    }

    public function toSql(): string {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";

        if (! empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (! empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if (! empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }


    public function orderBy(string $column): self {
        $this->orders[] = "{$column} ASC";

        return $this;
    }

    public function orderByDesc(string $column): self {
        $this->orders[] = "{$column} DESC";

        return $this;
    }


    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self {
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";

        return $this;
    }

    /** @return array<int, object> */
    public function get(string $className = \stdClass::class): array {
        $sql = $this->toSql();
        $stmt = $this->db->query($sql, $this->bindValues);


        return $stmt->fetchAll(\PDO::FETCH_CLASS, $className);
    }
}
