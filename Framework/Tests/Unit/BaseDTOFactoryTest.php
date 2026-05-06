<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use Framework\BaseDTO;
use Framework\BaseDTOFactory;
use Framework\Container;
use Framework\Http\SessionInterface;
use Framework\Utils\Translator;
use PHPUnit\Framework\TestCase;

class BaseDTOFactoryTest extends TestCase
{
    private $sessionMock;
    private $translatorMock;
    private $factory;

    protected function setUp(): void
    {
        $this->sessionMock = $this->createMock(SessionInterface::class);
        $this->translatorMock = $this->createMock(Translator::class);

        // Limpamos o Container para injetar nosso Translator mockado
        Container::clearInstances();
        Container::bind(Translator::class, $this->translatorMock);

        $this->factory = new BaseDTOFactory($this->sessionMock);
    }

    /**
         * Test DTO creation for a guest user (not logged in)
         */
    public function test_it_creates_dto_for_guest_user(): void
    {
        // Setup mocks for a guest session
        $this->sessionMock->method('get')->with('debugModeOn')->willReturn(false);
        $this->sessionMock->method('pullInt')->with('id')->willReturn(0);
        $this->sessionMock->method('pullString')->with('errorMessage')->willReturn('Session expired');

        $this->translatorMock->method('language')->willReturn('en');

        $dto = $this->factory->create();

        $this->assertInstanceOf(BaseDTO::class, $dto);

        // AJUSTE AQUI: O Volt trata guest como ID 0
        $this->assertEquals(0, $dto->id);

        $this->assertEquals('en', $dto->language);
        $this->assertEquals('Session expired', $dto->errorMessage);
        $this->assertFalse($dto->debugModeOn);
    }

    /**
     * Test DTO creation for an authenticated user
     */
    public function test_it_creates_dto_for_logged_in_user(): void
    {
        // Setup mocks for a logged-in user
        $this->sessionMock->method('pullInt')->with('id')->willReturn(42);
        $this->sessionMock->method('pullString')
            ->willReturnMap([
                ['username', '', 'BillDev'],
                ['avatar', '👤', '🚀'],
            ]);

        $this->translatorMock->method('language')->willReturn('pt');

        $dto = $this->factory->create();

        $this->assertEquals(42, $dto->id);
        $this->assertEquals('BillDev', $dto->username);
        $this->assertEquals('🚀', $dto->avatar);
        $this->assertEquals('pt', $dto->language);
    }
}
