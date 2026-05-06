<?php

declare(strict_types=1);

namespace App\Components\Feed;

class CommentEntity
{
    public ?int $id = null;
    public ?int $idea_id = null;
    public ?int $author_id = null;
    public ?string $content = null;
    public ?int $rating = null;
    public ?string $created_at = null;
    public ?string $author_name = null;
}