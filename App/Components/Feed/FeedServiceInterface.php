<?php

declare(strict_types=1);

namespace App\Components\Feed;

interface FeedServiceInterface
{
    /** @return array<int, IdeaEntity> */
    public function getTimeline(): array;
    
    /** @return array<int, IdeaEntity> */
    public function getTimelineByRoom(?string $roomUuid = null): array;

    public function contributeToRoom(string $roomUuid, int $authorId, string $content): void;

    public function addComment(int $ideaId, int $authorId, string $content, ?int $rating): void;
}