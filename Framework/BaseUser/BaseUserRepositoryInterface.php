<?php

declare(strict_types=1);

namespace Framework\BaseUser;

interface BaseUserRepositoryInterface
{
    public function usernameAlreadyExists(string $username): bool;
    public function save(BaseUser $user): int;
    public function getUserByUsername(string $username): ?BaseUser;
    /** @return array<BaseUser> */
    public function filterUsers(string $user): array;
    public function getUserById(int $id): ?BaseUser;
    public function getUserByGoogleId(string $googleId): ?BaseUser;
    public function getUserByEmail(string $email): ?BaseUser;
    public function updateGoogleData(int $userId, string $googleId, string $avatarUrl): void;

}
