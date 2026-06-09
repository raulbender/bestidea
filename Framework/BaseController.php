<?php

declare(strict_types=1);

namespace Framework;

use Framework\Http\CsrfServiceInterface;
use Framework\Http\SessionInterface;
use Framework\Utils\Navigation;
use Framework\Http\ResponseDTO;
use Framework\Http\ScopedService;

/**
 * Classe BaseController
 * * Serve como o centro de comando para todos os controllers do navio.
 * Fornece atalhos para sessão, proteção CSRF e renderização de views.
 */
abstract class BaseController implements ScopedService {
    protected string $view;
    protected SessionInterface $session;
    protected CsrfServiceInterface $csrfService;
    protected BaseDTO $baseDTO;
    protected BaseDTOFactory $dtoFactory;
    public static string $currentViewDir = '';
    public static ?BaseDTO $currentDTO = null;


    public function __construct() {
        $this->session = Container::resolve(SessionInterface::class);
        $this->dtoFactory = Container::resolve(BaseDTOFactory::class);
        $this->csrfService = Container::resolve(CsrfServiceInterface::class);
        $this->csrfService->checkCsrf();
        /**Inicializa o DTO que será compartilhado com todas as views */
        $this->baseDTO = $this->dtoFactory->create();
    }

    /**
     * Atalho para redirecionamento utilizando a classe Navigation.
     * Facilita para a IA sugerir fluxos de redirecionamento após ações.
     * * @param string $route A rota ou URL de destino.
     */
    protected function redirect(string $route): ResponseDTO {
        return Navigation::redirect($route);
    }

    /**
     * Atalho para definir mensagens de Flash (temporárias).
     * * DICA PARA IA: Você DEVE envolver a $message com a função de tradução __()
     * antes de passar para este método, ex: $this->flash('success', __('auth.success')).
     * * @param string $key A chave da mensagem (ex: 'success', 'error', 'info').
     * @param string $message A mensagem já traduzida.
     */
    protected function flash(string $key, string $message): void {
        $this->session->setFlash($key, $message);
    }

    /**
     * Carrega o conteúdo de uma view específica para ser injetado no layout.
     */
    public function loadViewContent(string $viewFullPath, object $data = null): string {
        $baseDTO = $this->baseDTO;
        ob_start();
        require $viewFullPath;

        return ensureString(ob_get_clean());
    }


    /**
     * Renderiza uma visão e injeta o objeto DTO padrão no layout.
     * * @param string $view Caminho da view (ex: 'user/profile').
     * @param string $layout Caminho do layout base (default: 'layout/layout').
     * @param object|null $data Dados adicionais específicos para esta view.
     * @param bool $isFramework Define se a view deve ser buscada na pasta interna do Framework.
     */
    protected function render(string $view, ?object $data = null, string $layout = 'layout/layout', bool $isFramework = false): ResponseDTO {
        $this->baseDTO->csrf_token = $this->csrfService->getToken();
        self::$currentDTO = $this->baseDTO;
        $baseDTO = $this->baseDTO;
        // Define o caminho da view
        $viewPath = $isFramework
            ? __DIR__ . '/Views/' . $view . ".phtml"
            : Container::$config->viewsPath . $view . ".phtml";

        $mainViewContent = $this->loadViewContent($viewPath, $data);

        $layoutPath = $isFramework
            ? __DIR__ . '/Views/' . $layout . ".phtml"
            : Container::$config->viewsPath . $layout . ".phtml";

        ob_start();
        require $layoutPath;
        $fullHtml = ensureString(ob_get_clean());

        return new ResponseDTO(
            statusCode: 200,
            headers: ['Content-Type' => 'text/html; charset=UTF-8'],
            body: $fullHtml
        );
    }

    protected function json(mixed $data, int $statusCode = 200): ResponseDTO {
        return new ResponseDTO(
            statusCode: $statusCode,
            headers: ['Content-Type' => 'application/json'],
            body: (string) json_encode($data)
        );
    }

    protected function cookie(string $name, string $value, int $expire = 3600): string {
        return sprintf(
            "%s=%s; Path=/; HttpOnly; SameSite=Lax; Max-Age=%d",
            $name,
            $value,
            $expire
        );
    }
}
