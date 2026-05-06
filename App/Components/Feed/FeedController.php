<?php

declare(strict_types=1);

namespace App\Components\Feed;

use Framework\BaseController;
use Framework\Http\ResponseDTO;

class FeedController extends BaseController
{
    public function __construct(private FeedServiceInterface $feedService)
    {
        parent::__construct();
    }

    public function index(): ResponseDTO
    {        
        return $this->render('feed/feed');
    }

    public function getIdeasApi(): ResponseDTO
    {
        try {
            $ideas = $this->feedService->getTimeline();
            
            return $this->json($ideas);

        } catch (\Throwable $e) {
            return $this->json(['error' => 'Falha ao navegar no feed'], 500);
        }
    }
}