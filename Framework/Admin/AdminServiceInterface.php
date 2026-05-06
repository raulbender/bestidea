<?php

declare(strict_types=1);

namespace Framework\Admin;

interface AdminServiceInterface
{
    public function runDatabaseInit(string $token): AdminDTO;
    public function clearAllData(string $token): AdminDTO;
    public function fetchSpecificLog(string $token, string $fileName): AdminDTO;
    public function sqlExecute(string $token, string $sqlQuery): AdminDTO;
    public function resetSystemLogs(string $token): AdminDTO;
    public function testRedisConnection(string $token): AdminDTO;
}
