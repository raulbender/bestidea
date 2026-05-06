<?php

declare(strict_types=1);

namespace App\Components\Feed;

use Framework\Database\DatabaseInterface;

class FeedRepository implements FeedRepositoryInterface
{
    public function __construct(
        private DatabaseInterface $db
    ) {}

public function findAllWithAuthors(): array
{
    return $this->db->table('ideas')
        ->select('ideas.*', 'authors.name AS author_name', 'authors.avatar AS author_avatar')
        ->join('authors', 'ideas.author_id', '=', 'authors.id')
        ->orderByDesc('ideas.created_at')
        ->get(IdeaEntity::class); // Passamos a Entity para o get() retornar objetos tipados
}

    public function createIdea(IdeaEntity $idea): bool
    {
        return $this->db->insert('ideas', $idea);
    }
}