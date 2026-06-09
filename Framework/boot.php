<?php

declare(strict_types=1);

namespace Framework;

require_once dirname(__DIR__) . "/vendor/autoload.php";

require_once __DIR__ . '/Dependencies.php';
$appDependencies = dirname(__DIR__, 1) . '/App/Dependencies.php';

if (file_exists($appDependencies)) {
    require_once $appDependencies;
} else {
    throw new \Exception("Application dependency file not found: " . $appDependencies);
}

$config = new Config(
    dbHost: ensureEnv('DB_HOST'),
    dbName: ensureEnv('DB_NAME'),
    dbUser: ensureEnv('DB_USER'),
    dbPass: ensureEnv('DB_PASS'),
    dbPort: ensureEnv('DB_PORT') ?: '3306',
    adminToken: ensureEnv('TOKEN'),
    pepper: ensureEnv('PEPPER'),
    adminPath: ensureEnv('ADMIN_PATH'),
    adminEmail: ensureEnv('ADMIN_EMAIL'),
    geminiKey: ensureEnv('GEMINI_KEY'),
    stripePublishableKey: ensureEnv('STRIPE_PUBLISHABLE_KEY'),
    stripeSecretKey: ensureEnv('STRIPE_SECRET_KEY'),
    webhookSecret: ensureEnv('WHSEC'),
    redisHost: ensureEnv('REDIS_HOST') ?: '127.0.0.1',
    googleClientId: ensureEnv('GG_ID'),
    googleClientSecret: ensureEnv('GG_KEY'),
    googleRedirectUri: ensureEnv('GG_REDIRECT_URI'),
    resendApiKey: ensureEnv('RESEND_API_KEY'),
    isDevEnvironment: ensureEnv('DEV_ENVIRONMENT'),
    rootPath: dirname(__DIR__, 1),
    viewsPath: dirname(__DIR__, 1) . '/App/Views/',
    storagePath: dirname(__DIR__, 1) . '/storage/',
    afterLoginRedirect: '/timeline',
);

Container::set($config);
date_default_timezone_set('UTC');

if (isDevEnvironment()) {
    define('LOG_CONFIG', [
        'DEBUG' => 1,
        'INFO' => 1,
        'SQL' => 1,
        'WARNING' => 1,
        'ERROR' => 1,
        'FATAL' => 1,
    ]);
} else {
    define('LOG_CONFIG', [
        'DEBUG' => 0,
        'INFO' => 1,
        'SQL' => 0,
        'WARNING' => 1,
        'ERROR' => 1,
        'FATAL' => 1,
    ]);
}