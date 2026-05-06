<?php

declare(strict_types=1);

namespace App\Components\Feed;

class FeedService implements FeedServiceInterface
{
    public function __construct(
        private FeedRepositoryInterface $repository
    ) {}

    public function getTimeline(): array
    {
        // Aqui poderíamos ter lógicas de filtro, cache ou moderação no futuro
        return $this->repository->findAllWithAuthors();
    }

    public function publishIdea(array $data): bool
    {
        $idea = new IdeaEntity();
        // No futuro, pegaremos o author_id da sessão via AuthService
        $idea->author_id = (int) ($data['author_id'] ?? 1); 
        $idea->content = (string) ($data['content'] ?? '');

        return $this->repository->createIdea($idea);
    }
}