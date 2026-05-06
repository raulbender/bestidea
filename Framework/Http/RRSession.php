<?php
declare(strict_types=1);

namespace Framework\Http;

class RRSession implements SessionInterface 
{
    private array $data = [];
    private bool $changed = false;

    public function set(string $key, mixed $value): void {
        $this->data[$key] = $value;
        $this->changed = true;
    }

    public function get(string $key): mixed {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool {
        return isset($this->data[$key]);
    }

    public function remove(string $key): void {
        unset($this->data[$key]);
        $this->changed = true;
    }

    // Métodos de Helper (Açúcar Sintático)
    public function pullInt(string $key, int $default = 0): int {
        $val = $this->get($key);
        return is_numeric($val) ? (int)$val : $default;
    }

    public function pullString(string $key, string $default = ''): string {
        $val = $this->get($key);
        return is_string($val) ? $val : $default;
    }

    // Lógica de Autenticação na Sessão
    public function login(int $userId): void {
        $this->set('user_id', $userId);
        $this->regenerate(); // Segurança: troca o ID ao mudar privilégios
    }

    public function isLoggedIn(): bool {
        return $this->has('user_id');
    }

    public function getUserId(): ?int {
        return $this->has('user_id') ? (int)$this->get('user_id') : null;
    }

    public function logout(): void {
        $this->data = [];
        $this->changed = true;
    }

    public function setFlash(string $key, string $message): void {
        $this->set("_flash_$key", $message);
    }

    public function regenerate(): void {
        $this->set('_regenerate', true); // Sinal para o middleware trocar o ID
    }

    // Métodos de Controle para o Middleware
    public function hydrate(array $data): void { $this->data = $data; }

    public function all(): array { return $this->data; }
    public function hasChanges(): bool { return $this->changed; }
    public function shouldRegenerate(): bool { return $this->get('_regenerate') === true; }
}