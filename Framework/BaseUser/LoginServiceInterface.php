<?php

declare(strict_types=1);

namespace Framework\BaseUser;

interface LoginServiceInterface
{
    public function login(string $username, string $password): bool;
    public function loginWithUser(BaseUser $user): bool;


}
