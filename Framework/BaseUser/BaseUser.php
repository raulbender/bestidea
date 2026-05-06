<?php

declare(strict_types=1);

namespace Framework\BaseUser;

abstract class BaseUser
{
    public function __construct(
        public ?string $username = null,
        public ?string $password = null,
        public ?string $email = null,
        public ?int $id = null,
        public ?string $created_at = null,
        public ?string $google_id = null,
        public ?string $avatar_url = null
    ) {
    }

    public function hashPassword(string $pepper): void
    {
        $peppered = hash_hmac("sha256", ensureString($this->password), $pepper);
        $this->password = password_hash($peppered, PASSWORD_ARGON2ID);
    }


    public function validate(): void
    {

        if (mb_strlen(ensureString($this->username)) < 3) {
            throw new \InvalidArgumentException('validation.user_short');
        }

        if (mb_strlen(ensureString($this->username)) > 50) {
            throw new \InvalidArgumentException('validation.user_long');
        }

        if (trim(ensureString($this->username)) !== ensureString($this->username)) {
            throw new \InvalidArgumentException('validation.user_spaces');
        }

        if (str_contains(ensureString($this->username), '  ')) {
            throw new \InvalidArgumentException('validation.user_multiple_spaces');
        }

        if (! preg_match('/^[\\p{L}0-9_ ]+$/u', ensureString($this->username))) {
            throw new \InvalidArgumentException('validation.user_invalid_characters');
        }

        if (strlen($this->password ?? '') < 4) {
            throw new \InvalidArgumentException('validation.user_password_too_short');
        }


    }



}
