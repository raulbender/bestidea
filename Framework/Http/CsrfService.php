<?php

declare(strict_types=1);

namespace Framework\Http;

use Framework\Container;
use Framework\Utils\Logger\Logger;

class CsrfService implements CsrfServiceInterface {
    private const SESSION_KEY = '_csrf_token';
    /** @var array<string> $allowedPaths */
    private array $allowedPaths = ['/payment'];

    public function __construct(private SessionInterface $session) {
    }


    public function checkCsrf(): void {
        $request = Container::resolve(Request::class);
        $path = $request->getPath();

        if (in_array($path, $this->allowedPaths)) {
            return;
        }

        if ($request->isPost()) {
            $token = $request->pullString('_token');

            if (!$token) {
                $token = $request->getHeader('X-CSRF-TOKEN');
            }

            if (!$this->isValid($token)) {
                throw new \Exception("Invalid CSRF Token. Request denied for security reasons.", 403);
            }
        }
    }



    public function getToken(): string {
        if (! $this->session->has(self::SESSION_KEY)) {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $this->session->pullString(self::SESSION_KEY);
    }


    public function isValid(string $submittedToken): bool {
        $storedToken = $this->session->pullString(self::SESSION_KEY);
        if (empty($storedToken) || empty($submittedToken)) {
            return false;
        }

        return hash_equals($storedToken, $submittedToken);
    }
}
