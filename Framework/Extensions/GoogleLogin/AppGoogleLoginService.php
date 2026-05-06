<?php

declare(strict_types=1);

namespace Framework\Extensions\GoogleLogin;

use Framework\BaseUser\BaseUserRepositoryInterface;
use Framework\BaseUser\LoginServiceInterface;
use Framework\BaseUser\SignUpServiceInterface;

class AppGoogleLoginService
{
    private ?string $lastError = null;

    public function __construct(
        private GoogleOAuthClient $googleClient,
        private BaseUserRepositoryInterface $userRepository,
        private LoginServiceInterface $loginService,
        private SignUpServiceInterface $registerService,
    ) {
    }


    public function getLoginUrl(): string
    {
        return $this->googleClient->getAuthUrl();
    }


    public function processCallback(string $code): bool
    {
        $googleData = $this->googleClient->fetchGoogleUser($code);

        if (! $googleData) {
            $this->lastError = 'error.google_auth_failed';

            return false;
        }

        $user = $this->userRepository->getUserByGoogleId(ensureString($googleData->google_id));

        if (! $user) {
            $user = $this->userRepository->getUserByEmail(ensureString($googleData->email));

            if ($user) {
                $this->userRepository->updateGoogleData(ensureInt($user->id), ensureString($googleData->google_id), ensureString($googleData->avatar_url));
            } else {
                $user = $this->registerService->registerNewUser($googleData);
                if (! $user) {
                    return false;
                }
            }
        }

        $this->loginService->loginWithUser($user);

        return true;
    }


    public function getErrorMessage(): string
    {
        return __($this->lastError ?? 'error.unknown');
    }


}
