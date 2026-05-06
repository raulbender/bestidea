<?php

declare(strict_types=1);

namespace Tests\Unit;

use Framework\BaseRoute;
use Framework\Container;
use Framework\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * Stub concreto para testar a lógica da BaseRoute
 */
class RouteTestStub extends BaseRoute
{
    protected function initRoutes(): void
    {
        $this->routes['test_route'] = [
            'route' => '/test-uri',
            'controller' => \Framework\Admin\AdminController::class,
            'action' => 'index',
        ];
    }
}

class RouteTest extends TestCase
{
    private $requestMock;

    protected function setUp(): void
    {
        Container::clearInstances();
        $this->requestMock = $this->createMock(Request::class);
    }

    public function test_if_setup_route_has_valid_controllers_and_actions(): void
    {
        $router = new RouteTestStub($this->requestMock);

        foreach ($router->getRoutes() as $params) {
            $controller = $params['controller'];
            $action = $params['action'];

            $this->assertTrue(class_exists($controller), "Classe $controller não existe.");
            $this->assertTrue(method_exists($controller, $action), "Método $action não existe em $controller.");
        }
    }

    public function test_get_url_strips_query_parameters(): void
    {
        // MANOBRA DE PRECISÃO: Configuramos o retorno de forma isolada
        $this->requestMock->expects($this->once())
            ->method('uri')
            ->willReturn('/timeline?post=123');

        $router = new RouteTestStub($this->requestMock);

        // O BaseRoute usa parse_url para limpar a string
        $this->assertEquals('/timeline', $router->getUrl());
    }

    public function test_router_throws_404_exception_for_invalid_route(): void
    {
        $this->requestMock->expects($this->once())
            ->method('uri')
            ->willReturn('/rota-inexistente');

        $router = new RouteTestStub($this->requestMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(404);

        $router->run();
    }
}
