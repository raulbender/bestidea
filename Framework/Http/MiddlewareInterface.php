<?php
declare(strict_types=1);

namespace Framework\Http;

interface MiddlewareInterface extends ScopedService
{
    /**
     * Processa a requisição (A "Ida").
     * @return Request Retorna a requisição possivelmente modificada com dados injetados.
     */
    public function processIncoming(Request $request): Request;

    /**
     * Processa a resposta (A "Volta").
     * Ideal para injetar headers, como o Set-Cookie da Sessão, antes de devolver ao RoadRunner.
     */
// Agora a volta recebe e devolve o pacote
    public function processOutgoing(ResponseDTO $response): ResponseDTO;
}