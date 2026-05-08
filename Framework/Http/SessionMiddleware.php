<?php

declare(strict_types=1);

namespace Framework\Http;

use Framework\Container;
use Framework\Extensions\Redis\RedisServiceInterface;

class SessionMiddleware implements MiddlewareInterface {
    private const COOKIE_NAME = 'VOLT_SESSID';
    private string $sessionId = '';
    private bool $isNewSession = false;

    public function processIncoming(Request $request): Request {
        $cookies = $this->parseCookies($request->getHeader('Cookie') ?? '');
        $volt_sessid = $cookies[self::COOKIE_NAME];
        
        $redis = Container::resolve(RedisServiceInterface::class);
        $rawContent = $redis->get("session:" . $volt_sessid);
        
        if ($rawContent === null) {
            $this->sessionId = bin2hex(random_bytes(32));
            $this->isNewSession = true;            
            $data = [];
        } else {
            $this->sessionId = $volt_sessid;
            $data = unserialize($rawContent);
        }        

        /** @var RRSession $session */
        $session = Container::resolve(SessionInterface::class);
        $session->hydrate($data);

        return $request;
    }
    

    public function processOutgoing(ResponseDTO $response): ResponseDTO {
        /** @var RRSession $session */
        $session = Container::resolve(SessionInterface::class);

        if ($session->hasChanges() || $this->isNewSession) {
            /** @var RedisServiceInterface $redis */
            $redis = Container::resolve(RedisServiceInterface::class);

            $redis->set(
                "session:" . $this->sessionId,
                serialize($session->all()),
                300 // (86400)24 hours
            );

            // Carimbamos o cookie no ResponseDTO que você acabou de criar![cite: 11, 16]
            $response->headers['Set-Cookie'] = sprintf(
                "%s=%s; Path=/; HttpOnly; SameSite=Lax; Max-Age=300",
                self::COOKIE_NAME,
                $this->sessionId
            );
        }

        return $response;
    }

    private function parseCookies(string $cookieHeader): array {
        parse_str(str_replace(['; ', ';'], '&', $cookieHeader), $cookies);
        return $cookies;
    }
}
