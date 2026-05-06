<?php

declare(strict_types=1);

namespace Framework;

use Framework\Admin\AdminController;
use Framework\Extensions\GoogleLogin\GoogleLoginController;
use Framework\Extensions\Payment\WebhookController;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;

abstract class BaseRoute {
    /** @var array<array<string,string>> */
    protected array $routes;

    abstract protected function initRoutes(): void;

    public function __construct(private Request $request) {
        $this->initFrameworkRoutes();
        $this->initRoutes();
    }


    /** @return array<array<string,string>> */
    public function getRoutes(): array {
        return $this->routes;
    }

    public function initFrameworkRoutes(): void {
        $adminBase = '/' . Container::$config->adminPath;

        $this->routes['admin_panel'] = [
            'route' => $adminBase,
            'controller' => AdminController::class,
            'action' => 'index',
        ];
        $this->routes['toggle_debug'] = [
            'route' => '/toggleDebugMode',
            'controller' => AdminController::class,
            'action' => 'toggleDebugMode',
        ];

        $this->routes['payment_webhook'] = [
            'route' => '/payment',
            'controller' => WebhookController::class,
            'action' => 'handleStripe',
        ];

        $this->routes['google_redirect'] = [
            'route' => '/login/google',
            'controller' => GoogleLoginController::class,
            'action' => 'redirectToGoogle',
        ];

        $this->routes['google_callback'] = [
            'route' => '/login/google/callback',
            'controller' => GoogleLoginController::class,
            'action' => 'handleGoogleCallback',
        ];
    }


    /** @param array<string, array{route: string, controller: string, action: string}> $routes */
    public function setRoutes(array $routes): void {
        $this->routes = $routes;
    }


    public function run(): ResponseDTO {
        $url = $this->getUrl();

        foreach ($this->getRoutes() as $key => $route) {
            if ($url == $route['route']) {

                $class = $route['controller'];
                if (! class_exists($class)) {
                    throw new \Exception("Controller class [$class] not found!", 500);
                }
                $controller = Container::resolve($class);
                $action = $route['action'];
                if (! method_exists($controller, $action)) {
                    throw new \Exception("Action [$action] not found in the " . get_class($controller), 500);
                }
                // A MÁGICA ACONTECE AQUI:
                // Capturamos o retorno do método (que agora é um ResponseDTO)
                /** @var ResponseDTO $response */
                $response = $controller->$action($this->request);

                return $response;
            }
        }
        http_response_code(404);

        throw new \Exception("Page not found!", 404);
    }


    public function getUrl(): string {
        return parse_url($this->request->uri(), PHP_URL_PATH) ?: "_SERVER[REQUEST_URI]_was_not_found";
    }
}
