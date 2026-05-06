<?php

declare(strict_types=1);

namespace Framework\Extensions\Redis;

use RuntimeException;

class RedisService implements RedisServiceInterface {
    public function __construct(private RedisConnectionInterface $connection) {
    }

    public function incr(string $key): int {
        $result = $this->connection->getClient()->incr($key);

        if ($result === false) {
            throw new RuntimeException("Falha na operação atômica do Redis para a chave: {$key}", 500);
        }

        return (int) $result;
    }


    public function setEntity(string $key, object $entity, int $ttl = null): bool {
        $data = $this->entityToRedisArray($entity);

        return $this->setHash($key, $data, $ttl);
    }


    public function getEntity(string $key, string $className): ?object {
        $data = $this->getHash($key);
        if (empty($data)) {
            return null;
        }

        $entity = new $className();

        $reflection = new \ReflectionClass($entity);

        foreach ($data as $prop => $value) {
            if (property_exists($entity, $prop)) {
                $property = $reflection->getProperty($prop);
                $type = $property->getType();

                if ($type instanceof \ReflectionNamedType && $type->getName() === 'int') {
                    $entity->$prop = (int)$value;
                } elseif ($type instanceof \ReflectionNamedType && $type->getName() === 'bool') {
                    $entity->$prop = (bool)$value;
                } else {
                    $entity->$prop = $value;
                }
            }
        }

        return $entity;
    }


    /** @return array<string, mixed> */
    private function entityToRedisArray(object $entity): array {
        $vars = get_object_vars($entity);
        $clean = [];

        foreach ($vars as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            if (is_object($value) || is_array($value)) {
                throw new \Exception("Redis Error: DTO property '$key' must be a scalar type (int, string, bool). Objects or Arrays are not allowed in Redis Hashes.");
            }

            $clean[$key] = is_bool($value) ? (int)$value : $value;
        }

        return $clean;
    }

    public function set(string $key, string $value, int $ttl = null): bool {
        $client = $this->connection->getClient();

        if ($ttl !== null) {
            return $client->setex($key, $ttl, $value);
        }

        return $client->set($key, $value);
    }


    public function get(string $key): ?string {
        $result = $this->connection->getClient()->get($key);

        return $result === false ? null : (string) $result;
    }


    public function setHash(string $key, array $data, int $ttl = null): bool {
        $client = $this->connection->getClient();

        if ($ttl !== null) {
            $results = $client->multi()
                ->hMSet($key, $data)
                ->expire($key, $ttl)
                ->exec();

            if ($results === false || in_array(false, $results, true)) {
                throw new \RuntimeException("Falha na operação multi/exec do Redis para a chave: {$key}");
            }

            return true;
        }

        $result = $client->hMSet($key, $data);

        if ($result === false) {
            throw new \RuntimeException("Falha crítica ao gravar Hash no Redis para a chave: {$key}");
        }

        return  true;
    }



    public function getHash(string $key): array {
        $result = $this->connection->getClient()->hGetAll($key);

        if ($result === false) {
            throw new \RuntimeException("Falha crítica ao ler Hash do Redis para a chave: {$key}");
        }

        return $result;
    }


    public function expire(string $key, int $seconds): bool {
        $result = $this->connection->getClient()->expire($key, $seconds);
        return (bool)$result;
    }



    public function keys(string $pattern): array {
        $result = $this->connection->getClient()->keys($pattern);

        if ($result === false) {
            throw new \RuntimeException("Falha ao executar KEYS no Redis com o padrão: {$pattern}");
        }

        return $result;
    }


    public function delete(string $key): int {
        return (int) $this->connection->getClient()->del($key);
    }



    public function zAdd(string $key, float $score, string $member): bool {
        $result = $this->connection->getClient()->zAdd($key, $score, $member);

        if ($result === false) {
            throw new \RuntimeException("Falha ao adicionar membro ao Sorted Set no Redis para a chave: {$key}");
        }

        return true;
    }


    public function zRevRange(string $key, int $start, int $stop): array {
        $result = $this->connection->getClient()->zRevRange($key, $start, $stop, true);

        if ($result === false) {
            throw new \RuntimeException("Falha ao executar ZREVRANGE no Redis para a chave: {$key}");
        }

        return $result;
    }




    public function zRevRank(string $key, string $member): ?int {

        $result = $this->connection->getClient()->zRevRank($key, $member);

        if ($result === false) {
            return null;
        }

        return $result;
    }


    public function zRem(string $key, string $member): int {
        $result = $this->connection->getClient()->zRem($key, $member);

        if ($result === false) {
            throw new \RuntimeException("Falha ao remover membro do Sorted Set no Redis para a chave: {$key}");
        }

        return $result;
    }
}
