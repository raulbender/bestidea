<?php

declare(strict_types=1);

namespace App\Components\Feed;

use Framework\Database\DatabaseInterface;

class FeedRepository implements FeedRepositoryInterface {
    public function __construct(
        private DatabaseInterface $db
    ) {
    }

    public function findAllWithAuthors(): array {
        return $this->db->table('ideas')
            ->select('ideas.*', 'authors.name AS author_name', 'authors.avatar AS author_avatar')
            ->join('authors', 'ideas.author_id', '=', 'authors.id')
            ->orderByDesc('ideas.created_at')
            ->get(IdeaEntity::class); 
    }

    // App/Components/Feed/FeedRepository.php

public function findAllByRoomUuid(string $roomUuid): array {
    return $this->db->table('ideas')
        ->select(
            'ideas.*', 
            'authors.name AS author_name', 
            'authors.avatar AS author_avatar'
        )
        ->join('authors', 'ideas.author_id', '=', 'authors.id')
        ->join('rooms', 'ideas.room_id', '=', 'rooms.id') // Join para chegar no UUID
        ->where('rooms.uuid', '=', $roomUuid)            // O filtro de segurança que você sugeriu
        ->orderByDesc('ideas.created_at')
        ->get(IdeaEntity::class);
}

    public function findCommentsByIdeaIds(array $ids): array {
        if (empty($ids)) return [];

        return $this->db->table('comments')
            ->select('comments.*', 'authors.name AS author_name', 'authors.avatar AS author_avatar')
            ->join('authors', 'comments.author_id', '=', 'authors.id')
            ->whereIn('comments.idea_id', $ids)
            ->orderBy('comments.created_at')
            ->get(CommentEntity::class); 
    }


    public function createIdea(IdeaEntity $idea): bool {
        return $this->db->insert('ideas', $idea);
    }
}
