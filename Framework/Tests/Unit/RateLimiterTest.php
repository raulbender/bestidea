<?php

declare(strict_types=1);

namespace Tests\Framework\Unit;

use PHPUnit\Framework\TestCase;
use Framework\Security\RateLimiter;
use Framework\Extensions\Redis\RedisServiceInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class RateLimiterTest extends TestCase
{
    private RedisServiceInterface $redisMock;
    private RateLimiter $rateLimiter;

    protected function setUp(): void
    {
        // Criamos um "dublê" do seu RedisService
        $this->redisMock = $this->createMock(RedisServiceInterface::class);
        
        // Injetamos o dublê no limitador
        $this->rateLimiter = new RateLimiter($this->redisMock);
    }

    public function testPrimeiraRequisicaoDefineOTtlNoRedis(): void
    {
        // Dizemos ao dublê: "Quando chamarem incr(), retorne 1"
        $this->redisMock->expects($this->once())
                        ->method('incr')
                        ->with('test_key')
                        ->willReturn(1);

        // Como retornou 1, esperamos que o expire() seja chamado com os 60 segundos
        $this->redisMock->expects($this->once())
                        ->method('expire')
                        ->with('test_key', 60)
                        ->willReturn(true);

        // Executamos a função. Se não lançar exceção, o teste passa.
        $this->rateLimiter->check('test_key', 5, 60);
        $this->assertTrue(true); 
    }

    public function testLancaExcecao429QuandoLimiteForExcedido(): void
    {
        // Simulamos que o Redis já registrou 6 requisições
        $this->redisMock->expects($this->once())
                        ->method('incr')
                        ->willReturn(6);

        // O expire NÃO deve ser chamado, pois não é a primeira requisição
        $this->redisMock->expects($this->never())->method('expire');

        // Dizemos ao PHPUnit que esperamos uma exceção específica
        $this->expectException(Exception::class);
        $this->expectExceptionCode(429);
        $this->expectExceptionMessage("Too Many Requests. Rate limit of 5 exceeded.");

        // Ao rodar isso, a exceção deve estourar
        $this->rateLimiter->check('test_key', 5, 60);
    }

// public function testResolveClientIpUsaCloudflarePrimeiro(): void
// {
//     // 1. Criamos o Mock
//     $requestMock = $this->createMock(\Framework\Http\Request::class);
    
//     // 2. CONFIGURAÇÃO CORRETA:
//     // O method() recebe apenas o NOME do método. 
//     // O willReturnMap recebe os argumentos e retornos esperados.
//     $requestMock->method('getHeader') 
//                 ->willReturnMap([
//                     ['CF-Connecting-IP', '203.0.113.50'],
//                     ['X-Forwarded-For', '198.51.100.1']
//                 ]);

//     // O resto do teste continua igual...
//     $this->redisMock->method('incr')->willReturn(1);

//     $this->redisMock->expects($this->once())
//                     ->method('incr')
//                     ->with($this->stringContains('203.0.113.50')); 

//     $this->rateLimiter->protectGeneralTraffic($requestMock);
// }
}