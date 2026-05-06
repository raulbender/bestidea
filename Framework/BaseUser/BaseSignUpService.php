<?php

declare(strict_types=1);

namespace Framework\BaseUser;

use Framework\Container;
use Framework\Utils\Logger\Logger;

class BaseSignUpService implements BaseSignUpServiceInterface
{
    public function __construct(
        private BaseUserRepositoryInterface $repository,
        public ?String $errorMessage = null,
        public ?string $username = null,
        public ?int $id = null
    ) {
    }

    public function create(BaseUser $newUser): ?int
    {
        try {
            $newUser->validate();

            if ($this->repository->usernameAlreadyExists(ensureString($newUser->username))) {
                $this->errorMessage = 'signup.username_taken';

                return null;
            }

            $pepper = Container::$config->pepper;
            if (! is_string($pepper) || $pepper === '') {
                Logger::error("CRITICAL SECURITY FLAW: PEPPER not defined in .env");
                $this->errorMessage = 'signup.error_message_unavailable';

                return null;
            }

            $newUser->hashPassword($pepper);
            $this->id = $this->repository->save($newUser);
            $this->username = $newUser->username;

            return $this->id;

        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();

            return null;
        }
    }


}
