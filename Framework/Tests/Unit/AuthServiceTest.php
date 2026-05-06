<?php

declare(strict_types=1);

namespace Tests\Unit;

use Framework\BaseUser\AuthService;
use Framework\BaseUser\BaseUser;
use Framework\BaseUser\BaseUserRepositoryInterface;
use Framework\Container;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $repositoryMock;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(BaseUserRepositoryInterface::class);
        $this->authService = new AuthService($this->repositoryMock);
    }

    private function prepareMockUser(): BaseUser
    {
        $user = new class () extends BaseUser {};
        $user->username = 'BillDev';
        $user->password = 'secret123';
        $user->hashPassword(Container::$config->pepper);

        return $user;
    }

    public function test_login_returns_user_with_correct_credentials(): void
    {
        $user = $this->prepareMockUser();
        $this->repositoryMock->method('getUserByUsername')->willReturn($user);

        $result = $this->authService->verifyCredentials('BillDev', 'secret123');

        $this->assertNotNull($result);
        $this->assertEquals('BillDev', $result->username);
    }

    public function test_login_returns_null_with_wrong_password(): void
    {
        $user = $this->prepareMockUser();
        $this->repositoryMock->method('getUserByUsername')->willReturn($user);

        $result = $this->authService->verifyCredentials('BillDev', 'wrong_password');

        $this->assertNull($result);
    }

    public function test_login_returns_null_if_user_not_found(): void
    {
        $this->repositoryMock->method('getUserByUsername')->willReturn(null);

        $result = $this->authService->verifyCredentials('UnknownUser', 'any_password');

        $this->assertNull($result);
    }


}
