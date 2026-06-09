<?php

namespace Framework;

class Config
{
    public function __construct(
        public readonly string $dbHost,
        public readonly string $dbName,
        public readonly string $dbUser,
        public readonly string $dbPass,
        public readonly string $dbPort,
        public readonly string $adminToken,
        public readonly string $pepper,
        public readonly string $adminPath,
        public readonly string $adminEmail,
        public readonly string $geminiKey,
        public readonly string $stripePublishableKey,
        public readonly string $stripeSecretKey,
        public readonly string $webhookSecret,
        public readonly string $rootPath,
        public readonly string $viewsPath,
        public readonly string $storagePath,
        public readonly string $redisHost,
        public readonly string $googleClientId,
        public readonly string $googleClientSecret,
        public readonly string $googleRedirectUri,
        public readonly string $afterLoginRedirect,
        public readonly string $resendApiKey,
        public readonly string $isDevEnvironment,        
    ) {
    }
}
