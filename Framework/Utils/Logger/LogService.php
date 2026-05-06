<?php

declare(strict_types=1);

namespace Framework\Utils\Logger;

use Framework\Http\Request;

class LogService
{
    private const STORAGE_LOG_PATH = __DIR__ . '/../../../storage/logs/';

    public function logLevelIsOn(string $level): bool
    {
        if (defined('PHPUNIT_RUNNING')) {
            return false;
        }
        $config = defined('LOG_CONFIG') ? (array) LOG_CONFIG : Logger::DEFAULT_LEVELS;

        return isset($config[$level]) && $config[$level] === 1;
    }

    public function process(string $level, string $message): void
    {
        $header = $this->generateHeader($level);
        $fullMessage = "{$header}[$message]" . PHP_EOL;

        $this->saveLog(strtolower($level), $fullMessage);

        file_put_contents('php://stderr', $fullMessage);
    }

    private function generateHeader(string $level): string
    {
        $request = new Request();
        $uri = $this->formatStringToNumChars($request->uri(), 20);
        $levelStr = $this->formatStringToNumChars($level, 5);
        $date = date('Y-m-d H:i:s');

        $memory = round(memory_get_usage() / 1024 / 1024, 2);
        $peak = round(memory_get_peak_usage() / 1024 / 1024, 2);

        $memoryTXT = $this->formatStringToNumChars("$date][MEM: {$memory}MB | PEAK: {$peak}MB", 47);

        return "[>$levelStr][$memoryTXT][$uri]";
    }

    private function saveLog(string $fileName, string $message): void
    {
        $path = self::STORAGE_LOG_PATH . $fileName . '.log';
        file_put_contents($path, $message, FILE_APPEND);
    }

    private function formatStringToNumChars(string $text, int $num): string
    {
        $truncated = substr($text, 0, $num);

        return str_pad($truncated, $num, " ", STR_PAD_RIGHT);
    }
}
