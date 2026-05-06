<?php

declare(strict_types=1);

namespace App\Components\Feed;

use Framework\BaseController;
use Framework\Http\ResponseDTO;
use Framework\Http\Request;
use Framework\Database\DatabaseInterface;
use Framework\Container;

/**
 * Controller responsável pelo Feed de Ideias do BestIdea.
 * Gerencia a renderização da View e a entrega do JSON para a API.
 */
class FeedController extends BaseController
{
    private DatabaseInterface $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Container::resolve(DatabaseInterface::class);
    }

    /**
     * Renderiza a página principal do Feed (feed.html).
     * Esta página é estática e o conteúdo é montado pelo feed.js.
     */
    public function index(): ResponseDTO
    {        
        return $this->render('feed/feed');
    }

    /**
     * Rota de API: /api/ideas
     * Retorna o JSON com postagens e comentários para o motor feed.js.
     */
    public function getIdeasApi(): ResponseDTO
    {
        try {
            // Simulando a busca no banco. Futuramente usaremos Repository.
            // A estrutura deve bater com o contrato do seu feed.js[cite: 9]
            $ideas = [
                [
                    "id" => "1",
                    "author" => ["name" => "Bill", "avatar" => "B"],
                    "content" => "Minha ideia para o framework Volt R²!",
                    "created_at" => "5 min atrás",
                    "comments" => [
                        ["id" => "c1", "author" => "Linus", "content" => "Excelente comando, Capitão!"]
                    ]
                ]
            ];

            // Retornamos como JSON puro para o Fetch API[cite: 5]
            $response = new ResponseDTO();
            $response->body = (string) json_encode($ideas);
            $response->headers = ['Content-Type' => 'application/json'];
            $response->statusCode = 200;

            return $response;

        } catch (\Throwable $e) {
            // Se algo der errado, o kernel do Volt trata ou enviamos erro 500
            $response = new ResponseDTO();
            $response->statusCode = 500;
            $response->body = (string) json_encode(['error' => 'Falha ao navegar no feed']);
            return $response;
        }
    }
}