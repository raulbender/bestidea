<?php

declare(strict_types=1);

namespace Framework\Admin;

use Framework\Container;

class AdminDTO
{
    /** @param array<string> $logFiles
     * @param array<string, mixed> $queryResults */
    public function __construct(
        public string $errorMessage = '',
        public string $logContent = '',
        public bool $success = false,
        public array $logFiles = [],
        public string $deployDate = '',
        public array $queryResults = []
    ) {
        $this->deployDate = ensureString(file_exists(Container::$config->storagePath . '/deploy_date.txt') ? file_get_contents(Container::$config->storagePath . '/deploy_date.txt') : 'Unknown');
    }
}
