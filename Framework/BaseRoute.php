<?php

declare(strict_types=1);

namespace Framework;

use Framework\Admin\AdminController;
use Framework\Extensions\GoogleLogin\GoogleLoginController;
use Framework\Extensions\Payment\WebhookController;
use Framework\Http\Request;
use Framework\Http\ResponseDTO;
use Framework\Http\ScopedService;
use Framework\Utils\Translator;

abstract class BaseRoute implements ScopedService {
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
            $routePath = $route['route'];

            // 1. A MÁGICA: Converte {lang} em uma expressão regular capturável
            // Exemplo: "/{lang}/feed" vira "#^/(?P<lang>[^/]+)/feed$#"
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $url, $matches)) {

                            // 2. GUARDANDO OS PARÂMETROS: 
                // Limpamos os matches para pegar apenas os que têm nome (como 'lang')
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // 2. INJEÇÃO NO REQUEST: Guarda cada parâmetro dentro do Request
                foreach ($params as $param => $value) {
                    $this->request->setAttribute((string)$param, $value);
                }

                $class = $route['controller'];
                if (! class_exists($class)) {
                    throw new \Exception("Controller class [$class] not found!", 500);
                }

                $controller = Container::resolve($class);
                $action = $route['action'];

                if (! method_exists($controller, $action)) {
                    throw new \Exception("Action [$action] not found in the " . get_class($controller), 500);
                }



                /** @var ResponseDTO $response */
                $response = $controller->$action($this->request);

                return $response;
            }
        }

        throw new \Exception("Page not found!", 404);
    }


    public function generateUrl(string $name, array $params = []): string {
        if (!isset($this->routes[$name])) {
            throw new \Exception("Rota [$name] não encontrada!");
        }

        $url = $this->routes[$name]['route'];

        // Se a rota pede {lang} e você não passou, o sistema se auto-ajuda
        if (str_contains($url, '{lang}') && !isset($params['lang'])) {
            $params['lang'] = Container::resolve(Translator::class)->language();
        }

        foreach ($params as $key => $value) {
            $url = str_replace("{{$key}}", (string)$value, $url);
        }

        return $url;
    }


    public function getUrl(): string {
        return parse_url($this->request->uri(), PHP_URL_PATH) ?: "_SERVER[REQUEST_URI]_was_not_found";
    }
}
