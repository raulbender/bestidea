<?php

declare(strict_types=1);

namespace Framework\BaseUser;

interface BaseSignUpServiceInterface
{
    public function create(BaseUser $newUser): ?int;
}
