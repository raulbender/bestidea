<?php

declare(strict_types=1);

// IMPRESCINDÍVEL: Impede que avisos ou erros do PHP quebrem a comunicação binária
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

require __DIR__ . '/vendor/autoload.php';

use Spiral\RoadRunner\Http\PSR7Worker;
use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Worker;
use Framework\Container;
use Framework\Http\Request;
use Framework\Http\Kernel;
use Framework\Utils\Error\ErrorHandler;

\Sentry\init([
    'dsn' => ensureEnv('SENTRY_DSN'),  // Specify a fixed sample rate
    'traces_sample_rate' => 1.0,
    // Set a sampling rate for profiling - this is relative to traces_sample_rate
    'profiles_sample_rate' => 1.0,
    // Enable logs to be sent to Sentry
    'enable_logs' => true,
    'environment' => isDevEnvironment() ? 'development' : 'production',
]);

ErrorHandler::register();

try {
    require_once __DIR__ . '/Framework/boot.php';
} catch (\Throwable $e) {
    // Se o boot.php falhar, o handleBootException seria chamado pelo PHP, 
    // mas o try/catch aqui é mais seguro para o RoadRunner.
    ErrorHandler::handleBootException($e);
}


// A forma moderna de criar o Worker (resolve o relay automaticamente)
$psr17Factory = new Psr17Factory();
$worker = new PSR7Worker(
    Worker::create(),
    new Psr17Factory(),
    new Psr17Factory(),
    new Psr17Factory()
);


while ($psrRequest = $worker->waitRequest()) {
    try {
        handleRequest($psrRequest, $worker, $psr17Factory);
    } catch (\Throwable $e) {
        handleFatalError($e, $worker, $psr17Factory);
    } finally {
        Container::clearRequestInstances();
    }
}


function handleRequest($psrRequest, $worker, $factory) {
    Container::clearRequestInstances();
    $request = Request::createFromPsr7($psrRequest);
    Container::bind(Request::class, $request);

  
    /** @var Kernel $kernel */
    $kernel = Container::resolve(Kernel::class);
    $responseDTO = $kernel->handle($request);

    // Converte nosso DTO para a resposta PSR-7 do RoadRunner[cite: 11, 14]
    $psr7Response = $factory->createResponse($responseDTO->statusCode);
    $psr7Response->getBody()->write($responseDTO->body);

    // Injeta os Headers (incluindo o Set-Cookie do middleware)
    foreach ($responseDTO->headers as $name => $value) {
        $psr7Response = $psr7Response->withHeader($name, $value);
    }

    $worker->respond($psr7Response);

}


function handleFatalError($e, $worker, $factory) {

    $error = ErrorHandler::convertToResponse($e);

    try {
        $response = $factory->createResponse($error->status)->withHeader('Content-Type', 'text/html');
        $response->getBody()->write($error->html);
        $worker->respond($response);
    } catch (\Throwable $fatal) {
        fwrite(STDERR, date('Y-m-d H:i:s') . " ERRO NO WORKER AO RESPONDER: " . $fatal->getMessage() . PHP_EOL);        
    }

    if ($error->status >= 500) {
        exit(1);
    }
}
