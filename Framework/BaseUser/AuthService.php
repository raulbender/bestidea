<?php

declare(strict_types=1);

namespace Framework\BaseUser;

use Framework\Container;
use Framework\Utils\Logger\Logger;

class AuthService
{
    public function __construct(
        private BaseUserRepositoryInterface $userRepository,
    ) {
    }

    public function verifyCredentials(string $username, string $password): ?BaseUser
    {
        $user = $this->userRepository->getUserByUsername($username);

        $pepper = Container::$config->pepper;
        if (! is_string($pepper) || $pepper === '') {
            Logger::error("FATAL ERROR: PEPPER variable not configured.");

            return null;
        }
        $pepperedPassword = hash_hmac("sha256", $password, $pepper);

        if ($user && (password_verify($pepperedPassword, (string)$user->password))) {
            return $user;
        }

        return null;
    }



}
