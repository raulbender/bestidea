<?php

declare(strict_types=1);

namespace Framework\Security;

use Framework\Extensions\Redis\RedisServiceInterface;
use Framework\Http\Request;
use Exception;

class RateLimiter
{
    public function __construct(
        private RedisServiceInterface $redisService
    ) {}

    /**
     * Aplica o limite de Janela Fixa Descentralizada (TTL-Based Fixed Window).
     * @throws Exception
     */
    public function check(string $key, int $limit, int $ttlSeconds): void
    {
        $currentCount = $this->redisService->incr($key);

        // Se for a primeira requisição (a chave acabou de nascer), definimos o TTL exato dela.
        if ($currentCount === 1) {
            $this->redisService->expire($key, $ttlSeconds);
        }

        if ($currentCount > $limit) {
            throw new Exception("Too Many Requests. Rate limit of {$limit} exceeded.", 429);
        }
    }

    /**
     * Proteção de tráfego geral para o Worker.
     * Exemplo: 15 requisições a cada 15 segundos.
     * @throws Exception
     */
    public function protectGeneralTraffic(Request $request): void
    {
        $ip = $this->resolveClientIp($request);
        $key = "rate_limit:general:{$ip}";
        
        $this->check($key, 60, 60);
    }

    /**
     * Proteção rigorosa especificamente para o fluxo de autenticação (OAuth).
     * Exemplo: 5 tentativas de iniciar o fluxo de login por minuto.
     * @throws Exception
     */
    public function protectLoginFlow(Request $request): void
    {
        $ip = $this->resolveClientIp($request);
        $key = "rate_limit:login:{$ip}";
        
        $this->check($key, 5, 60);
    }

    /**
     * Resolve o IP real do cliente. 
     * Fundamental para quem usa Cloudflare na "gringa", pois o REMOTE_ADDR será do servidor da CF.
     */
    private function resolveClientIp(Request $request): string
    {
        // Cloudflare IP header
        $cfIp = $request->getHeader('CF-Connecting-IP');
        if ($cfIp !== null && $cfIp !== '') {
            return $cfIp;
        }

        // Padrão de proxy reverso
        $forwarded = $request->getHeader('X-Forwarded-For');
        if ($forwarded !== null && $forwarded !== '') {
            // Se houver múltiplos IPs, o primeiro é o do cliente original
            $ips = explode(',', $forwarded);
            return trim($ips[0]);
        }

        // Fallback local
        return '127.0.0.1';
    }
}