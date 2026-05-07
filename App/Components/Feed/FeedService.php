<?php

declare(strict_types=1);

namespace App\Components\Feed;

class FeedService implements FeedServiceInterface {
    public function __construct(
        private FeedRepositoryInterface $repository
    ) {
    }

    public function getTimeline(): array {
        /** @var array <IdeaEntity> $ideas */
        $ideas = $this->repository->findAllWithAuthors(); 

        if (empty($ideas)) return [];

        $ideaIds = array_map(fn($idea) => $idea->id, $ideas);

        /** @var array <CommentEntity> $allComments */
        $allComments = $this->repository->findCommentsByIdeaIds($ideaIds);

        $commentsGrouped = [];
        foreach ($allComments as $comment) {
            $commentsGrouped[$comment->idea_id][] = [
                'author'  => __($comment->author_name),
                'avatar'  => $comment->author_avatar,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'rating'     => (int) ($comment->rating ?? 0)                
            ];
        }

        foreach ($ideas as $idea) {
            $idea->author_name = __($idea->author_name); 
            $idea->comments = $commentsGrouped[$idea->id] ?? []; 
        }

        return $ideas;
    }


    public function publishIdea(array $data): bool {
        $idea = new IdeaEntity();
        $idea->author_id = (int) ($data['author_id'] ?? 1);
        $idea->content = (string) ($data['content'] ?? '');

        return $this->repository->createIdea($idea);
    }
}
