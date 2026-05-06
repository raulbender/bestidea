<?php

declare(strict_types=1);

namespace Framework\Http;

use Framework\Container;
use Framework\Security\RateLimiter;
use App\Route;

class Kernel {
    public function handle(Request $request): ResponseDTO {

        /** @var SessionMiddleware $sessionMiddleware */
        $sessionMiddleware = Container::resolve(MiddlewareInterface::class);
        $request = $sessionMiddleware->processIncoming($request);

        /** @var RateLimiter $rateLimiter */
        $rateLimiter = Container::resolve(RateLimiter::class);
        $rateLimiter->protectGeneralTraffic($request);

        
        $router = Container::resolve(Route::class);
        $responseDTO = $router->run();

        $responseDTO = $sessionMiddleware->processOutgoing($responseDTO);
        return $responseDTO;
    }
}