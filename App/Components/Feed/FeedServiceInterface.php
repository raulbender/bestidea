<?php

declare(strict_types=1);

namespace App\Components\Feed;

interface FeedServiceInterface
{
    /** @return array<int, IdeaEntity> */
    public function getTimeline(): array;

    /** @param array<string, mixed> $data */
    public function publishIdea(array $data): bool;
}