<?php

declare(strict_types=1);

namespace App\Components\Feed;

interface FeedServiceInterface
{
    /** @return array<int, IdeaEntity> */
    public function getTimeline(): array;
    
    public function getTimelineByRoom(?string $roomUuid = null): array;

    /** @param array<string, mixed> $data */
    public function publishIdea(array $data): bool;

    public function contributeToRoom(string $roomUuid, int $authorId, string $content): void;
}