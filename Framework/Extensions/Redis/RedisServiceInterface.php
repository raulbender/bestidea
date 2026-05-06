<?php

declare(strict_types=1);

namespace Framework\Extensions\Redis;

interface RedisServiceInterface
{
    public function set(string $key, string $value, int $ttl = null): bool;
    public function get(string $key): ?string;
    public function setEntity(string $key, object $entity, int $ttl = null): bool;
    public function getEntity(string $key, string $className): ?object;
    /** @param array<string, mixed> $data */
    public function setHash(string $key, array $data, int $ttl = null): bool;
    /** @return array<string, string> */
    public function getHash(string $key): array;
    public function expire(string $key, int $seconds): bool;
    /** @return array<int, string> */
    public function keys(string $pattern): array;
    public function delete(string $key): int;
    public function zAdd(string $key, float $score, string $member): bool;
    /** @return array<string, float> */
    public function zRevRange(string $key, int $start, int $stop): array;
    public function zRevRank(string $key, string $member): ?int;
    public function zRem(string $key, string $member): int;
    /** Incrementa um valor atômico no Redis e retorna o novo total */
    public function incr(string $key): int;

}
