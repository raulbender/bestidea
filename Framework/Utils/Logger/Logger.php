<?php

declare(strict_types=1);

namespace Framework\Utils\Logger;

use Framework\Container;

class Logger
{
    public const DEFAULT_LEVELS = [
        'DEBUG' => 1, 'INFO' => 1, 'SQL' => 1,
        'WARNING' => 1, 'PATH' => 1, 'FATAL' => 1, 'ERROR' => 1,
    ];

    public static function error(string $message): void
    {
        $path = __DIR__ . '/../../../storage/logs/error.log';
        file_put_contents($path, $message . "\n", FILE_APPEND);
    }

    public static function info(string $message): void
    {
        self::dispatch('INFO', $message);
    }

    /** @param array<string|int, mixed> $params   */
    public static function sql(string $query, array $params = []): void
    {
        $paramString = ! empty($params) ? " | Params: " . json_encode($params) : "";
        self::dispatch('SQL', $query . $paramString);
    }

    public static function warning(string $message): void
    {
        $path = __DIR__ . '/../../../storage/logs/warning.log';
        file_put_contents($path, $message . "\n", FILE_APPEND);
    }

    private static function dispatch(string $level, string $message): void
    {
        $service = Container::resolve(LogService::class);
        if ($service->logLevelIsOn($level)) {
            $service->process($level, $message);
        }
    }
}
