<?php

declare(strict_types=1);

namespace App\Components\Feed;

use App\Components\Author\AuthorRepositoryInterface;
use App\Components\Room\RoomServiceInterface;
use App\Components\Robots\AvocadoRobot;
use Framework\Extensions\AI\Gemini\GeminiClient;
use Framework\Container;

class FeedService implements FeedServiceInterface {
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
        private RoomServiceInterface $roomService,
        private AuthorRepositoryInterface $authorRepository
    ) {
    }

    public function contributeToRoom(string $roomUuid, int $authorId, string $content): void {
        $room_id = $this->roomService->getRoomByUuid($roomUuid)->id;
        $idea = new IdeaEntity();
        $idea->author_id = $authorId;
        $idea->room_id = $room_id;
        $idea->content = $content;

        $lastIdeaId = $this->feedRepository->createIdea($idea);
        $room = $this->roomService->getRoomByUuid($roomUuid);
        $authorName = $this->authorRepository->getAuthorById($authorId)->name;
        $authorName = __($authorName);
        $this->generateEvaluationFromAvocadoRobot($content, $lastIdeaId, $room->description, $authorName);
    }

    private function generateEvaluationFromAvocadoRobot(string $idea, int $ideaId, string $description, string $authorName): void {
        $avocadoRobot = new AvocadoRobot(Container::$config->geminiKey);
        $geminiClient = new GeminiClient($avocadoRobot);
        $prompt = "Contexto da Sala: {$description}\n";
        $prompt .= "Autor da Ideia: {$authorName}\n";
        $prompt .= "Ideia para analisar: " . $idea;

        $history = [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ];
        $feedback = $geminiClient->generateResponseFromRobot($avocadoRobot, $history);
        if (!$feedback) return;

        $rate = $avocadoRobot->extractRatingFromFeedback($feedback);
        $feedbackClean = preg_replace('/\[NOTA:\d\]/', '', $feedback);
        $this->addComment($ideaId, $avocadoRobot->id, trim($feedbackClean), $rate);
    }

    public function addComment(int $ideaId, int $authorId, string $content, ?int $rating): void {
        $comment = new CommentEntity();
        $comment->idea_id = $ideaId;
        $comment->author_id = $authorId;
        $comment->content = $content;
        $comment->rating = $rating;

        $this->feedRepository->createComment($comment);
    }

    public function getTimeline(): array {
        /** @var array <IdeaEntity> $ideas */
        $ideas = $this->feedRepository->findAllWithAuthors();

        if (empty($ideas)) return [];

        $ideaIds = array_map(fn($idea) => $idea->id, $ideas);

        /** @var array <CommentEntity> $allComments */
        $allComments = $this->feedRepository->findCommentsByIdeaIds($ideaIds);

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


    public function getTimelineByRoom(?string $roomUuid = null): array {
        if ($roomUuid) {
            /** @var array <IdeaEntity> $ideas */
            $ideas = $this->feedRepository->findAllByRoomUuid($roomUuid);
        }

        if (empty($ideas)) return [];

        $ideaIds = array_map(fn($idea) => $idea->id, $ideas);
        /** @var array <CommentEntity> $allComments */
        $allComments = $this->feedRepository->findCommentsByIdeaIds($ideaIds);

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
            $idea->average_rating = $idea->average_rating !== null ? (float) $idea->average_rating : null;
            $idea->total_comments = $idea->total_comments !== null ? (int) $idea->total_comments : null;
        }

        return $ideas;
    }

}
