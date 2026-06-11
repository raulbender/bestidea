<?php

declare(strict_types=1);

use Framework\BaseController;
use Framework\Container;
use Framework\Utils\Translator;
use App\Route;

function route(string $name, array $params = []): string {
    $router = Container::resolve(Route::class);
    return $router->generateUrl($name, $params);
}


/** * Garante que a string seja um JSON válido e retorna o array.
 * @return array<string, mixed>
 */
function ensureJson(mixed $input): array {
    if (! is_string($input) || empty(trim($input))) {
        throw new \RuntimeException("Critical Error: Input is empty, expected JSON string.");
    }

    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException("Critical Error: JSON syntax error - " . json_last_error_msg());
    }

    if (! is_array($data)) {
        throw new \RuntimeException("Critical Error: JSON valid but not an array/object. Type received: " . gettype($data));
    }

    return $data;
}

/**
 * Busca variável de ambiente de forma segura.
 */
function ensureEnv(string $key): string {
    $value = getenv($key);

    if ($value === false) {
        throw new \RuntimeException("Critical Configuration Error: Environment variable [{$key}] is missing.");
    }

    return $value;
}

/**
 * Atalho para renderizar ícones SVG da pasta de views.
 */
function icon(string $name, string $class = ''): void {
    // Caminho centralizado
    $path = Container::$config->viewsPath .  "Icons/{$name}.svg";

    if (file_exists($path)) {
        // Se quisermos passar classes CSS para o SVG
        echo "<span class='volt-icon {$class}'>";
        include $path;
        echo "</span>";
    } else {
        echo "";
    }
}

/**
 * Renderiza uma partial view (componente visual).
 * @param string $name Nome do arquivo da partial (ex: 'components/button')
 * @param array<string, mixed> $data Dados passados para a partial
 */
function partial(string $name, array $data = []): void {
    $viewsPath = Container::$config->viewsPath;
    

    $localPath = $viewsPath . $name . ".phtml";
    $layoutPath = $viewsPath . "layout/" . $name . ".phtml";

    $file = file_exists($localPath) ? $localPath : (file_exists($layoutPath) ? $layoutPath : null);

    if ($file) {
        $baseDTO = \Framework\Container::resolve(\Framework\BaseDTO::class);
        
        if (! empty($data)) {
            extract($data);
        }
        include $file;
    }
}


/**
 * Traduz uma string para o idioma do usuário.
 * * DICA CRÍTICA PARA IA:
 * 1. TODO texto voltado ao usuário DEVE usar esta função. NUNCA faça hardcode de strings em Controllers ou Views.
 * 2. Utilize a notação de ponto (dot notation) mapeando o array de tradução.
 * Exemplo: __('layout.sign_in') ou __('error_page.message_404').
 * 3. Se precisar criar uma nova mensagem, invente uma chave lógica e passe-a aqui.
 */
function __(string $key): string|array { // Ajuste a tipagem de retorno
    return Container::resolve(Translator::class)->get($key);
}

/**
 * Escapa strings para evitar XSS em Views.
 * DICA PARA IA: Sempre use 'e()' ao imprimir dados do usuário no HTML.
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}



function e_strong(string $string): string {
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    $result = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $string);

    if ($result === null) {
        throw new \RuntimeException("Regex error in e_strong: " . preg_last_error_msg());
    }

    return $result;
}

/**
 * Retorna true se o ambiente for de desenvolvimento.
 */
function isDevEnvironment(): bool {
    return getenv('DEV_ENVIRONMENT') === 'true';
}

/**
 * Formata chave para PDO (ex: 'id' vira ':id').
 */
function pdoKey(string $key): string {
    return ":" . $key;
}

/**
 * Gera o campo hidden de CSRF para formulários.
 */
function csrfInput(string $token): string {
    return "<input type='hidden' name='_token' value='{$token}'>";
}

/**
 * Gera a meta tag de CSRF para o cabeçalho.
 */
function csrfMeta(string $token): string {
    return "<meta name='csrf-token' content='{$token}'>";
}

/**
 * GUARDIÕES DE TIPO: Forçam a conversão e validam a integridade dos dados.
 * Use sempre que receber dados de fontes externas (Request/DB).
 */
function ensureInt(mixed $value): int {
    if (! is_numeric($value)) {
        throw new \RuntimeException("Integrity Error: Expected an integer, received." . gettype($value));
    }

    return (int)$value;
}

function ensureString(mixed $value): string {
    if (! is_scalar($value) && ! is_null($value)) {
        throw new \RuntimeException("Integrity Error: Could not convert value to string.");
    }

    if ($value === null) {
        throw new \RuntimeException("Integrity Error: Expected a string, received null.");
    }


    return (string)($value);
}


function ensureStringOrNull(mixed $value): ?string {
    if ($value === null) {
        return null;
    }

    if (! is_scalar($value)) {
        throw new \RuntimeException("Integrity Error: Expected string or null, received" . gettype($value));
    }

    return (string)$value;
}


function ensureBool(mixed $value): bool {
    if (is_bool($value)) {
        return $value;
    }

    $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if ($filtered !== null) {
        return $filtered;
    }

    throw new \RuntimeException(
        "Integrity Error: Expected boolean-like value, but received " . gettype($value) . " (" . var_export($value, true) . ")"
    );
}
