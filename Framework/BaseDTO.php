<?php

declare(strict_types=1);

namespace Framework;

use Framework\Http\ScopedService;

class BaseDTO implements ScopedService
{
    public function __construct(
        public int $id = 0,
        public string $username = 'LOGIN',
        public string $avatar = '👤',
        public string $csrf_token = '',
        public bool $debugModeOn = false,
        public string $language = 'en',
        public bool $loginError = false,
        public bool $navHidden = false,
        public string $bodyClass = 'themeDefault',
        public ?string $errorMessage = null,
    ) {
    }

}
