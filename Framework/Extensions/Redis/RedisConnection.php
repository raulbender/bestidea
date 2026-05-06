<?php

declare(strict_types=1);

namespace Framework\Extensions\Redis;

use Framework\Container;
use RuntimeException;
use Redis;

class RedisConnection implements RedisConnectionInterface {
    private ?Redis $client = null;
    private string $host;
    private int $port;

    public function __construct() {
        $this->host = Container::$config->redisHost ?: '127.0.0.1';
        $this->port = 6379;
    }


    public function getClient(): Redis {
        if ($this->client === null) {
            $this->connect();
        }

        return $this->client ?? throw new RuntimeException("Redis client não inicializado.");
    }

    private function connect(): void {
        $this->client = new Redis();

        $connected = $this->client->pconnect($this->host, $this->port);

        if (!$connected) {
            throw new RuntimeException("Não foi possível conectar ao Redis em {$this->host}:{$this->port}", 500);
        }
    }
}
