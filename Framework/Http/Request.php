<?php

declare(strict_types=1);

namespace Framework\Http;

class Request implements ScopedService {
    private function __construct(
        private array $query = [],
        private array $post = [],
        private array $server = [],
        private ?string $content = null,
        private array $headers = []
    ) {
    }


    /**
     * Cria uma instância a partir de uma requisição PSR-7 (RoadRunner).
     */
    public static function createFromPsr7(\Psr\Http\Message\ServerRequestInterface $psrRequest): self {
        $instance = new self(
            $psrRequest->getQueryParams(),
            $psrRequest->getParsedBody() ?? [],
            $psrRequest->getServerParams(),
            (string) $psrRequest->getBody()
        );

        $instance->server['REQUEST_URI'] = $psrRequest->getUri()->getPath();
        $instance->server['REQUEST_METHOD'] = $psrRequest->getMethod();

        // Normalizamos as chaves para lowercase no momento da criação
        foreach ($psrRequest->getHeaders() as $name => $values) {
            $instance->headers[strtolower((string)$name)] = $values;
        }

        return $instance;
    }


    /** @return array<string, mixed>*/
    public function all(): array {
        return array_merge($this->query, $this->post);
    }

    public function getBaseUrl(): string {
        $protocol = (isset($this->server['HTTPS']) && $this->server['HTTPS'] === 'on') ? "https" : "http";
        $host = $this->getHeader('host') ?? $this->server['HTTP_HOST'] ?? 'localhost';
        $host = ensureString($host);

        return "{$protocol}://{$host}";
    }

    /**
     * Puxa um dado da requisição (Post ou Query).
     * @param string $key Chave do dado.
     * @param mixed $default Valor padrão caso não exista.
     */
    public function pull(string $key, mixed $default = null): mixed {
        $data = $this->all();

        return $data[$key] ?? $default;
    }

    /**
     * Puxa um dado garantindo que seja string.
     */
    public function pullString(string $key, string $default = ''): string {
        $value = $this->pull($key);

        return is_scalar($value) ? (string) $value : $default;
    }

    /**
     * Puxa um dado garantindo que seja inteiro.
     */
    public function pullInt(string $key, int $default = 0): int {
        $value = $this->pull($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Puxa e converte para booleano (ideal para checkboxes e flags).
     */
    public function pullBool(string $key, bool $default = false): bool {
        $value = $this->pull($key);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Verifica se a chave existe na requisição (mesmo que vazia).
     */
    public function has(string $key): bool {
        return array_key_exists($key, $this->all());
    }

    public function method(): string {
        return ensureString($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string {
        return ensureString($this->server['REQUEST_URI']);
    }

    public function isPost(): bool {
        return $this->method() === 'POST';
    }

    public function getHeader(string $name): ?string {
        $values = $this->headers[strtolower($name)] ?? null;

        return $values ? implode(', ', (array)$values) : null;
    }

    public function getPath(): string {
        $result = parse_url($this->uri(), PHP_URL_PATH);

        return ensureString($result);
    }

    /** @return array<string, mixed> */
    public function getJson(): array {
        $raw = $this->content ?? file_get_contents('php://input');

        return ensureJson($raw);
    }
}
