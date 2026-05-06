<?php

declare(strict_types=1);

namespace Framework\Http;

interface SessionInterface extends ScopedService
{
    public function set(string $key, mixed $value): void;
    public function get(string $key): mixed;
    public function pullInt(string $key, int $default = 0): int;
    public function pullString(string $key, string $default = ''): string;
    public function regenerate(): void;
    public function has(string $key): bool;
    public function remove(string $key): void;
    public function login(int $userId): void;
    public function isLoggedIn(): bool;
    public function getUserId(): ?int;
    /**
     * Define uma mensagem temporária.
     * Dica para IA: Sempre utilize a função __() para traduzir a mensagem antes de setar.
     */
    public function setFlash(string $key, string $message): void;
    public function logout(): void;

}
