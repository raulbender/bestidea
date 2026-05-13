<?php

declare(strict_types=1);

namespace App\Components\Feed;

use Framework\BaseController;
use Framework\Http\ResponseDTO;
use Framework\Http\Request;

class FeedController extends BaseController {
    public function __construct(private FeedServiceInterface $feedService) {
        parent::__construct();
    }


    public function contributeApi(Request $request): ResponseDTO {
        $roomUuid = $request->getAttribute('uuid');

        $data = $request->getJson();
        $content = $data['content'] ?? null;

        if (!$content || mb_strlen($content) < 5) {
            return $this->json(['error' => 'Conteúdo inválido ou muito curto.'], 400);
        }

        $cookieName = "auth_room_{$roomUuid}";
        $cookies = $request->getCookieParams();
        $authorId = (int) ($cookies[$cookieName] ?? 0);

        if ($authorId === 0) {
            return $this->json(['error' => 'Autor não identificado na sala.'], 403);
        }

        $this->feedService->contributeToRoom($roomUuid, $authorId, $content);

        return $this->json([
            'status' => 'success',
            'message' => 'Contribuição recebida com sucesso!'
        ]);
    }

    public function commentApi(Request $request): ResponseDTO {
        $ideaId = (int) $request->getAttribute('idea_id');

        $data = $request->getJson();
        $content = $data['content'] ?? null;
        $rating = isset($data['rating']) ? (int) $data['rating'] : null;

        $roomUuid = $request->getAttribute('uuid');
        $cookieName = "auth_room_{$roomUuid}";
        $cookies = $request->getCookieParams();
        $authorId = (int) ($cookies[$cookieName] ?? 0);

        if (!$content || mb_strlen($content) < 3) {
            return $this->json(['error' => 'Comentário inválido ou muito curto.'], 400);
        }

        if ($rating !== null && ($rating < 1 || $rating > 5)) {
            return $this->json(['error' => 'Avaliação deve ser entre 1 e 5.'], 400);
        }

        $this->feedService->addComment($ideaId, $authorId, $content, $rating);

        return $this->json([
            'status' => 'success',
            'message' => 'Comentário enviado com sucesso!'
        ]);
    }   


    public function getIdeasApi(Request $request): ResponseDTO {
        try {
            $roomUuid = $request->getAttribute('uuid');

            $ideas = $this->feedService->getTimelineByRoom($roomUuid);

            return $this->json($ideas);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Falha ao navegar no feed'], 500);
        }
    }
}
