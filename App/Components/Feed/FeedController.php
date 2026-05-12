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

    // public function index(): ResponseDTO
    // {        
    //     return $this->render('feed/feed');
    // }

    public function getIdeasApi_Old(): ResponseDTO {
        try {
            $ideas = $this->feedService->getTimeline();

            return $this->json($ideas);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Falha ao navegar no feed'], 500);
        }
    }

    // App/Components/Feed/FeedController.php

    public function getIdeasApi(Request $request): ResponseDTO {
        try {
            // Tentamos pegar o room_id (que será o UUID) da query string
            $roomUuid = $request->getAttribute('uuid');

            $ideas = $this->feedService->getTimelineByRoom($roomUuid);

            return $this->json($ideas);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Falha ao navegar no feed'], 500);
        }
    }
}
