<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use Exception;
use Framework\Http\SessionInterface;
use Framework\Http\RRSession;
use Framework\Container;
use PHPUnit\Framework\TestCase;
use Framework\Extensions\Redis\RedisService;
use Framework\Extensions\Redis\RedisServiceInterface;

// Dummy classes for testing purposes
class DependencyC {
}
class DependencyB {
    public function __construct(public DependencyC $c) {
    }
}
class DependencyA {
    public function __construct(public DependencyB $b) {
    }
}

class ScalarDependency {
    public function __construct(public string $version = '1.0.0') {
    }
}

class ContainerTest extends TestCase {
    protected function setUp(): void {
        // Limpa o estado do container antes de cada teste
        Container::clearInstances();
        Container::clearRequestInstances();
    }

    public function test_it_resolves_simple_class_without_constructor(): void {
        $instance = Container::resolve(DependencyC::class);

        $this->assertInstanceOf(DependencyC::class, $instance);
    }

    public function test_it_acts_as_a_singleton_by_default(): void {
        $instance1 = Container::resolve(DependencyC::class);
        $instance2 = Container::resolve(DependencyC::class);

        // As instâncias devem ser exatamente o mesmo objeto (referência)
        $this->assertSame($instance1, $instance2);
    }

    public function test_it_resolves_nested_dependencies_automatically(): void {
        // Container deve resolver A -> B -> C
        $instance = Container::resolve(DependencyA::class);

        $this->assertInstanceOf(DependencyA::class, $instance);
        $this->assertInstanceOf(DependencyB::class, $instance->b);
        $this->assertInstanceOf(DependencyC::class, $instance->b->c);
    }

    

    public function test_it_respects_manual_bindings(): void {
        // Criamos a instância manualmente
        $manualInstance = new DependencyC();

        // Fazemos o bind do OBJETO direto, não da função
        Container::bind(DependencyC::class, $manualInstance);

        $instance = Container::resolve(DependencyC::class);

        $this->assertInstanceOf(DependencyC::class, $instance);
        $this->assertSame($manualInstance, $instance, "Deve retornar a instância exata que foi bindada manualmente.");
    }

    public function test_it_resolves_constructor_with_default_scalar_values(): void {
        $instance = Container::resolve(ScalarDependency::class);

        $this->assertEquals('1.0.0', $instance->version);
    }

    public function test_it_throws_exception_for_unresolvable_scalar_parameter(): void {
        // Uma classe com parâmetro string sem valor padrão
        $classWithScalar = new class('test') {
            public function __construct(string $name) {
            }
        };

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("I can't resolve the scalar parameter");

        Container::resolve(get_class($classWithScalar));
    }

    public function test_it_throws_exception_for_non_instantiable_class(): void {
        // Interfaces não podem ser instanciadas sem um bind prévio
        $this->expectException(Exception::class);

        // Usando uma interface que existe no seu código
        Container::resolve(\DateTimeInterface::class);
    }


    /**
     * TESTE 1: Verifica se o Container resolve a mesma instância para a Interface.
     */
    public function testShouldReturnSameInstanceWhenResolvingInterface(): void {
        // Configuramos o bind da Interface para a Classe Concreta[cite: 18]
        Container::bind(SessionInterface::class, RRSession::class);

        // Resolvemos pela primeira vez via Interface
        $instance1 = Container::resolve(SessionInterface::class);

        // Resolvemos pela segunda vez via Interface
        $instance2 = Container::resolve(SessionInterface::class);

        // Se o singleton funcionar, elas devem ser EXATAMENTE o mesmo objeto (referência)
        $this->assertSame($instance1, $instance2, "O Container criou instâncias diferentes para a mesma Interface!");
    }

    /**
     * TESTE 2: Verifica se o balde Scoped (requestInstances) é limpo corretamente.
     */
    public function testShouldClearScopedInstancesBetweenRequests(): void {
        Container::bind(SessionInterface::class, RRSession::class);

        $instanceBefore = Container::resolve(SessionInterface::class);

        // Simula o final da requisição no Worker
        Container::clearRequestInstances();

        $instanceAfter = Container::resolve(SessionInterface::class);

        // Devem ser objetos diferentes após a limpeza
        $this->assertNotSame($instanceBefore, $instanceAfter, "O Container não limpou a instância Scoped após o clearRequestInstances!");
    }

    /**
     * TESTE 3: Valida o Bloqueio de Instanciação Direta.
     * Agora, o Container DEVE lançar uma exceção se alguém tentar burlar a Interface.
     */
    public function testShouldThrowExceptionWhenResolvingConcreteClassThatHasInterface(): void {
        Container::bind(SessionInterface::class, RRSession::class);

        // Primeiro, resolvemos corretamente via Interface
        $byInterface = Container::resolve(SessionInterface::class);
        $this->assertInstanceOf(RRSession::class, $byInterface);

        // Agora, tentamos o acesso proibido via Classe Concreta
        $this->expectException(\Exception::class);

        Container::resolve(RRSession::class);
    }

    /**
     * TESTE 4: Valida que classes sem interface (Folhas) ainda funcionam.
     * Isso garante que a trava não quebrou a funcionalidade básica para classes simples.
     */
    public function testShouldStillResolveSimpleClassesWithoutInterfaces(): void {
        // DependencyC não tem bind nem interface no nosso setup de teste
        $instance1 = Container::resolve(DependencyC::class);
        $instance2 = Container::resolve(DependencyC::class);

        $this->assertInstanceOf(DependencyC::class, $instance1);
        $this->assertSame($instance1, $instance2, "Classes folha devem continuar funcionando como Singletons.");
    }
}
