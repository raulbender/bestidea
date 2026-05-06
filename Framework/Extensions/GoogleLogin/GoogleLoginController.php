<?php

declare(strict_types=1);

namespace Framework\Extensions\GoogleLogin;

use Framework\BaseController;
use Framework\Container;
use Framework\Http\Request;
use Framework\Security\RateLimiter;
use Framework\Utils\Navigation;

class GoogleLoginController extends BaseController {
    public function __construct(
        private AppGoogleLoginService $googleLoginService
    ) {
        parent::__construct();
    }


    public function redirectToGoogle(Request $request): void {
        /** @var RateLimiter $rateLimiter */
        $rateLimiter = Container::resolve(RateLimiter::class);
        $rateLimiter->protectLoginFlow($request);

        $url = $this->googleLoginService->getLoginUrl();
        Navigation::redirect($url);
    }


    public function handleGoogleCallback(Request $request): void {
        $code = $request->pullString('code');

        if (! $code) {
            throw new \Exception('google_auth_failed no code provided');
        }

        $success = $this->googleLoginService->processCallback($code);

        if ($success) {
            Navigation::redirect(Container::$config->afterLoginRedirect);
        }

        throw new \Exception($this->googleLoginService->getErrorMessage());
    }
}
