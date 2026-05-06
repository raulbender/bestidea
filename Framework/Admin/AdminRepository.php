<?php

namespace Framework\Admin;

use Framework\Database\DatabaseInterface;

class AdminRepository implements AdminRepositoryInterface
{
    public function __construct(private DatabaseInterface $db)
    {
    }

    public function executeRawSql(string $sql): void
    {
        $this->db->query($sql);
    }


    public function queryRawSql(string $sql): array
    {
        $statement = $this->db->query($sql);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
