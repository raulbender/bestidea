#!/usr/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Framework\Container;
use Framework\Console\Kernel;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

require_once __DIR__ . '/Framework/boot.php';

echo "🚀 Volt FPM - Console\n\n";
try {
    $kernel = Container::resolve(Kernel::class);
    $kernel->handle($argv);
} catch (\Exception $e) {
    echo "\n❌ [VOLT FATAL ERROR]: " . $e->getMessage() . "\n";
    exit(1);
}