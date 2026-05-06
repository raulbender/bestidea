<?php

declare(strict_types=1);

namespace Framework\Utils\Error;

use Framework\Utils\Logger\Logger;

class ErrorHandler {
    public static function register(): void {
        set_error_handler([self::class, 'handleWarning']);
        set_exception_handler([self::class, 'handleBootException']);
    }

    /**
     * MISSÃO 1: Tratar warnings e notices. 
     * Apenas logamos para não poluir o Sentry com bobagens.
     */
    public static function handleWarning(int $errno, string $errstr, string $errfile, int $errline): bool {
        $warningMessage = date('Y-m-d H:i:s') .
            " [errono]: " . $errno .
            " [errstr]: " . $errstr .
            " [errfile]: " . $errfile .
            " [errline]: " . $errline;
        Logger::warning($warningMessage);
        return true;
    }

    /**
     * MISSÃO 2: Tratar exceções que ocorrem ANTES do loop (no boot.php).
     * Aqui o sistema morre (exit 1).
     */
    public static function handleBootException(\Throwable $e): void {
        $errorMessage = date('Y-m-d H:i:s') .
            " [BOOT FATAL]: " . $e->getMessage() .
            " in " . $e->getFile() .
            ":" . $e->getLine();

        Logger::error($errorMessage);

        if (!isDevEnvironment()) {
            \Sentry\captureException($e);
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        fwrite(STDERR, $errorMessage . " | PHP:" . PHP_VERSION_ID . PHP_EOL);
        exit(1);
    }

    /**
     * MISSÃO 3: A função que você chama manualmente no try/catch do Worker.
     * Ela decide se vai pro Sentry e devolve o DTO ErrorResponse.
     */
    public static function convertToResponse(\Throwable $e): ErrorResponse {
        $code = (int) $e->getCode();
        if ($code < 100 || $code > 599) $code = 500;

        if ($code >= 500) {
            \Sentry\captureException($e);
        }

        $errorMessage = date('Y-m-d H:i:s') .
            " [Request Error] [$code]: " . $e->getMessage() .
            " in " . $e->getFile() .
            ":" . $e->getLine();
        Logger::error($errorMessage);

        while (ob_get_level() > 0) ob_end_clean();

        ob_start();
        if (isDevEnvironment()) {
            self::renderDebugPage($e);
        } else {
            self::renderErrorPage($code);
        }
        $html = ob_get_clean() ?: "Erro Crítico no Motor Volt";

        return new ErrorResponse($code, $html);
    }



    private static function renderErrorPage(int $code): void {
        $errorFile = __DIR__ . "/../../Views/errors/{$code}.phtml";

        if (file_exists($errorFile)) {
            include $errorFile;
            return;
        }

        echo "<h1>Fatal Error {$code}</h1><p>Volt Framework: Emergency power only.</p>";
    }

    private static function renderDebugPage(\Throwable $e): void {

        $trace = htmlspecialchars($e->getTraceAsString());

        echo "<html>
                <head>
                    <title>Volt ⚡ Error Debug</title>
                    <style>
                        body { background: #0f172a; color: #f1f5f9; font-family: 'Fira Code', 'Cascadia Code', monospace; padding: 40px; line-height: 1.5; }
                        .container { max-width: 1000px; margin: 0 auto; }
                        .header { border-left: 4px solid #ef4444; padding-left: 20px; margin-bottom: 30px; }
                        h1 { color: #ef4444; font-size: 24px; margin: 0; text-transform: uppercase; }
                        .msg { font-size: 18px; margin-top: 10px; color: #f87171; }
                        .file-info { background: #1e293b; padding: 15px; border-radius: 8px; border: 1px solid #334155; margin-bottom: 20px; }
                        .file-info b { color: #38bdf8; }
                        pre { background: #020617; padding: 20px; border-radius: 8px; border: 1px solid #1e293b; overflow-x: auto; color: #94a3b8; font-size: 13px; }
                        h3 { color: #38bdf8; font-size: 16px; margin-top: 30px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>[Volt Debug] Fatal Error</h1>
                            <div class='msg'>" . htmlspecialchars($e->getMessage()) . "</div>
                        </div>
                        
                        <div class='file-info'>
                            <b>File:</b> " . $e->getFile() . " <br>
                            <b>Line:</b> " . $e->getLine() . "
                        </div>

                        <h3>Stack Trace</h3>
                        <pre>{$trace}</pre>
                    </div>
                </body>
             </html>";
    }
}
