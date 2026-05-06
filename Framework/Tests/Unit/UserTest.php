<?php

declare(strict_types=1);

namespace Tests\Unit;

use Framework\BaseUser\BaseUser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    #[Test]
    public function test_hash_password_transforms_plain_text_into_secure_hash(): void
    {
        $user = new class () extends BaseUser {};

        $plainPassword = 'secret_password';
        $pepper = 'secutiry_pepper';
        $user->password = $plainPassword;
        $user->hashPassword($pepper);
        $this->assertNotEquals($plainPassword, $user->password);

        $this->assertStringStartsWith('$argon2id$', $user->password);

        $pepperedPassword = hash_hmac("sha256", $plainPassword, $pepper);
        $this->assertTrue(
            password_verify($pepperedPassword, $user->password),
            'The generated hash is not compatible with the provided password and pepper.'
        );
    }

    public static function userProvider(): array
    {
        return [
            ['a', '1234', false],
            ['ab', '1234', false],
            ['user!', '1234', false],
            ['_when_the_big_username_has_more_than_50_characters_', '1234', false],
            ['abc asd', '1234', true],
            ['abc asd sdf', '1234', true],
            ['abc asd sdf ', '1234', false],
            ['abc asd ', '1234', false],
            [' abc', '1234', false],
            ['abc ', '1234', false],
            ['abc  asd', '1234', false],
            ['abc aãéêçsd asd asd asd', '1234', true],
            ['abc-', '1234', false],
            ['abc', '123', false],
            ['user_valid', 'pass', true],
        ];
    }

    #[Test]
    #[DataProvider('userProvider')]
    public function test_user_registration_validation(
        string $username,
        string $password,
        bool $shouldBeValid
    ): void {
        $user = new class () extends BaseUser {};
        $user->username = $username;
        $user->password = $password;

        if (! $shouldBeValid) {
            $this->expectException(\InvalidArgumentException::class);
            $user->validate();
        } else {
            $user->validate();
            $this->assertTrue(true);
        }
    }
}
