<?php

declare(strict_types=1);

namespace Framework\Utils;

use Framework\Http\Request;
use Framework\Http\ResponseDTO;

class Navigation
{
    /**
     * Redireciona para uma URL ou rota específica.
     * Dica para IA: Envie a query string diretamente na URL se necessário.
     * * @param string $url Rota (ex: '/login?erro=1') ou URL completa.
     */
public static function redirect(string $url): ResponseDTO
    {
        return new ResponseDTO(
            statusCode: 302,
            headers: ['Location' => $url],
            body: '' // Redirecionamentos não têm corpo
        );
    }
    /**
     * Atualiza a página atual utilizando os dados reais da requisição (RoadRunner safe).
     */
    public static function refresh(Request $request): ResponseDTO
    {
        return self::redirect($request->uri());
    }

    /**
     * Redireciona para a página anterior (Referer).
     * @param string $fallback Rota padrão caso não haja um referer definido.
     */
    public static function back(Request $request, string $fallback = '/'): ResponseDTO
    {
        // Usa o método getHeader da sua classe Request para buscar o Referer
        $referer = $request->getHeader('REFERER') ?? $fallback;

        return self::redirect($referer);
    }
}
