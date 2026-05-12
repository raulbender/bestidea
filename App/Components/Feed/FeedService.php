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
                'created_at' => $comment->created_at . "Z",
                'rating'     => (int) ($comment->rating ?? 0)                
            ];
        }

        foreach ($ideas as $idea) {
            $idea->author_name = __($idea->author_name); 
            $idea->comments = $commentsGrouped[$idea->id] ?? []; 
            $idea->created_at .= "Z";
        }


        return $ideas;
    }

    // App/Components/Feed/FeedService.php

public function getTimelineByRoom(?string $roomUuid = null): array {
    if ($roomUuid) {
        $ideas = $this->repository->findAllByRoomUuid($roomUuid);
    } 
    
    if (empty($ideas)) return [];

    $ideaIds = array_map(fn($idea) => $idea->id, $ideas);
    $allComments = $this->repository->findCommentsByIdeaIds($ideaIds);

        $commentsGrouped = [];
        foreach ($allComments as $comment) {
            $commentsGrouped[$comment->idea_id][] = [
                'author'  => __($comment->author_name),
                'avatar'  => $comment->author_avatar,
                'content' => $comment->content,
                'created_at' => $comment->created_at . "Z",
                'rating'     => (int) ($comment->rating ?? 0)                
            ];
        }

        foreach ($ideas as $idea) {
            $idea->author_name = __($idea->author_name); 
            $idea->comments = $commentsGrouped[$idea->id] ?? []; 
            $idea->created_at .= "Z";
        }


    // ... o restante da sua lógica de agrupamento de comentários permanece IGUAL ...
    // Isso é o poder da boa arquitetura: você mudou a origem dos dados, 
    // mas a regra de negócio de como exibir comentários não mudou.
    
    return $ideas;
}


    public function publishIdea(array $data): bool {
        $idea = new IdeaEntity();
        $idea->author_id = (int) ($data['author_id'] ?? 1);
        $idea->content = (string) ($data['content'] ?? '');

        return $this->repository->createIdea($idea);
    }
}
