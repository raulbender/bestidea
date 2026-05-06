<?php

namespace Framework\Admin;

use Framework\Container;

class AdminService implements AdminServiceInterface
{
    private string $secretToken;

    public function __construct(private AdminRepositoryInterface $adminRepository)
    {
        $this->secretToken = Container::$config->adminToken;

    }

    // No AdminService.php
    public function testRedisConnection(string $token): AdminDTO
    {
        $dto = new AdminDTO();
        if (! $this->validateToken($token)) {
            $dto->errorMessage = "Access denied, pirate! 🏴‍☠️";

            return $dto;
        }

        try {
            $redis = Container::resolve(\Framework\Extensions\Redis\RedisConnectionInterface::class)->getClient();
            $pong = $redis->ping();
            $dto->success = true;
            $dto->errorMessage = "✅ REDIS STATUS: " . ($pong ? "PONG (Online e Operacional)" : "OFFLINE");
        } catch (\Exception $e) {
            $dto->errorMessage = "❌ REDIS FAILURE: " . $e->getMessage();
        }

        return $dto;
    }

    private function validateToken(string $token): bool
    {
        return hash_equals($this->secretToken, $token);
    }

    public function runDatabaseInit(string $token): AdminDTO
    {
        $dto = new AdminDTO();

        if (! $this->validateToken($token)) {
            $dto->errorMessage = "Access denied, pirate! 🏴‍☠️";

            return $dto;
        }

        try {
            $sqlFile = dirname(__DIR__, 2) . '/database/init.sql';
            if (! file_exists($sqlFile)) {
                throw new \Exception("File init.sql not found.");
            }

            $sql = (string)file_get_contents($sqlFile);
            $this->adminRepository->executeRawSql($sql);

            $dto->errorMessage = "✅ VICTORY! Database initialized.";
            $dto->success = true;
        } catch (\Exception $e) {
            $dto->errorMessage = "❌ FAILURE: " . $e->getMessage();
        }

        return $dto;
    }


    public function resetSystemLogs(string $token): AdminDTO
    {
        $dto = new AdminDTO();
        if (! $this->validateToken($token)) {
            $dto->errorMessage = "Access denied, pirate! 🏴‍☠️";

            return $dto;
        }

        $logPath = Container::$config->storagePath . 'logs/';

        try {
            $files = glob($logPath . '*.log') ?: [];

            foreach ($files as $file) {
                $f = fopen($file, 'w');
                if ($f !== false) {
                    fclose($f);
                }
            }

            $dto->success = true;
            $dto->errorMessage = "✅ Logs reseted! All clear on the horizon.";
        } catch (\Exception $e) {
            $dto->errorMessage = "❌ Error resetting logs: " . $e->getMessage();
        }

        return $dto;
    }


    public function sqlExecute(string $token, string $sqlQuery): AdminDTO
    {
        $dto = new AdminDTO();

        try {
            $trimmedSql = strtolower(trim($sqlQuery));

            if (str_starts_with($trimmedSql, 'select') || str_starts_with($trimmedSql, 'show')) {
                $dto->queryResults = $this->adminRepository->queryRawSql($sqlQuery);
                $dto->success = true;
                $dto->errorMessage = "✅ Query returned " . count($dto->queryResults) . " lines.";
            } else {
                $this->adminRepository->executeRawSql($sqlQuery);
                $dto->success = true;
                $dto->errorMessage = "✅ Command executed successfully.";
            }
        } catch (\Exception $e) {
            $dto->errorMessage = "❌ Error in SQL: " . $e->getMessage();
        }

        return $dto;
    }

    public function clearAllData(string $token): AdminDTO
    {
        $dto = new AdminDTO();

        if (! $this->validateToken($token)) {
            $dto->errorMessage = "Access denied, pirate! 🏴‍☠️";

            return $dto;
        }

        try {
            $sqlFile = Container::$config->rootPath . '/database/clear.sql';

            if (! file_exists($sqlFile)) {
                throw new \Exception("File clear.sql not found at: " . $sqlFile);
            }

            $sql = (string)file_get_contents($sqlFile);
            $this->adminRepository->executeRawSql($sql);

            // file_put_contents(Container::$config->storagePath . 'example.txt', "");
            // $deleteFile = Container::$config->storagePath . 'example_2.txt';
            // if (file_exists($deleteFile)) {
            //     unlink($deleteFile);
            // }

            $dto->errorMessage = "✅ VICTORY! Database cleared and Storage reset.";
            $dto->success = true;

        } catch (\Exception $e) {
            $dto->errorMessage = "❌ FAILURE: " . $e->getMessage();
        }

        return $dto;
    }




    public function fetchSpecificLog(string $token, string $fileName): AdminDTO
    {
        $dto = new AdminDTO();
        if (! $this->validateToken($token)) {
            $dto->errorMessage = "Access denied, pirate! 🏴‍☠️";

            return $dto;
        }

        $fileName = basename($fileName);

        $logPath = Container::$config->storagePath . 'logs/' . $fileName;

        if (file_exists($logPath)) {
            $dto->logContent = (string)file_get_contents($logPath);
            $dto->success = true;
        } else {
            $dto->errorMessage = "File $fileName do not exist on the radar.";
        }

        return $dto;



    }
}
