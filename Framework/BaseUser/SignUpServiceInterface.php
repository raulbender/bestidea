<?php

declare(strict_types=1);

namespace Framework\BaseUser;

interface SignUpServiceInterface
{
    public function create(BaseUser $user): bool;
    public function getErrorMessage(): string;
    public function registerNewUser(BaseUser $googleData): ?BaseUser;

}
