<?php

namespace Framework\Admin;

interface AdminRepositoryInterface
{
    public function executeRawSql(string $sql): void;
    /** @return array<array<string, mixed>> */
    public function queryRawSql(string $sql): array;
}
