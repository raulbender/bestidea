<?php

declare(strict_types=1);

namespace Framework\Http;

class PHPSession implements SessionInterface
{
    private const FLASH_KEY = '_flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => false, // Set to true if using HTTPS
                'cookie_samesite' => 'Lax',
                'use_only_cookies' => true,
            ]);
        }
    }

    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = $message;
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }


    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }


    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }


    public function pullInt(string $key, int $default = 0): int
    {
        $value = $this->get($key);

        return is_int($value) ? $value : $default;
    }


    public function pullString(string $key, string $default = ''): string
    {
        $value = $this->get($key);

        return is_string($value) ? $value : $default;
    }


    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }


    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function login(int $userId): void
    {
        $this->set('id', $userId);
        $this->regenerate();
    }

    public function isLoggedIn(): bool
    {
        return $this->has('id');
    }

    public function getUserId(): ?int
    {
        $id = $this->pullInt('id');

        return $id > 0 ? $id : null;
    }

    public function logout(): void
    {        
        $this->regenerate();
        $this->remove('id');
        $_SESSION = [];
        session_destroy();
    }
}
