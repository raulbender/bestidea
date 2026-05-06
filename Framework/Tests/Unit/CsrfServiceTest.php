<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use Framework\Container;
use Framework\Http\CsrfService;
use Framework\Http\Request;
use Framework\Http\SessionInterface;
use PHPUnit\Framework\TestCase;

class CsrfServiceTest extends TestCase
{
    private $sessionMock;
    private $csrfService;

    protected function setUp(): void
    {
        $this->sessionMock = $this->createMock(SessionInterface::class);
        $this->csrfService = new CsrfService($this->sessionMock);

        // Clear Container instances to avoid pollution between tests
        Container::clearInstances();
        Container::clearRequestInstances();
        
    }

    /**
     * Test if it generates and stores a new token if none exists
     */
    public function test_it_generates_and_stores_new_token_when_empty(): void
    {
        $this->sessionMock->expects($this->once())
            ->method('has')
            ->with('_csrf_token')
            ->willReturn(false);

        $this->sessionMock->expects($this->once())
            ->method('set')
            ->with('_csrf_token', $this->isType('string'));

        $this->sessionMock->expects($this->any())
            ->method('pullString')
            ->with('_csrf_token')
            ->willReturn('generated_token_123');

        $token = $this->csrfService->getToken();

        $this->assertEquals('generated_token_123', $token);
    }

    /**
     * Test validation logic for matching tokens
     */
    public function test_it_validates_correct_token(): void
    {
        $token = 'secure_token_abc';

        $this->sessionMock->expects($this->any())
            ->method('pullString')
            ->with('_csrf_token')
            ->willReturn($token);

        $this->assertTrue($this->csrfService->isValid($token));
    }

    /**
     * Test validation failure for mismatched tokens
     */
    public function test_it_invalidates_wrong_token(): void
    {
        $this->sessionMock->expects($this->any())
            ->method('pullString')
            ->with('_csrf_token')
            ->willReturn('original_token');

        $this->assertFalse($this->csrfService->isValid('hacker_token'));
    }

    /**
     * Test if it throws 403 Exception on invalid POST request
     */
    public function test_it_throws_exception_on_invalid_csrf_post(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(403);

        // Setup Request Mock inside Container
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects($this->any())
            ->method('getPath')
            ->willReturn('/admin/save');

        $requestMock->expects($this->any())
            ->method('isPost')
            ->willReturn(true);

        $requestMock->expects($this->any())
            ->method('pullString')
            ->with('_token')
            ->willReturn('wrong_token');

        Container::bind(Request::class, $requestMock);

        $this->sessionMock->expects($this->any())
            ->method('pullString')
            ->willReturn('correct_token');

        $this->csrfService->checkCsrf();
    }

    /**
     * Test if allowed paths skip CSRF check
     */
    public function test_it_skips_check_for_allowed_paths(): void
    {
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects($this->any())
            ->method('getPath')
            ->willReturn('/payment');

        // This should not trigger any validation even if it's a POST
        Container::bind(Request::class, $requestMock);

        $this->csrfService->checkCsrf();

        // If we reach here without exception, test passed
        $this->assertTrue(true);
    }
}
