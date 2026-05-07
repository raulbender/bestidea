<?php

declare(strict_types=1);

namespace App\Components\Feed;

class AuthorEntity
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $avatar = null;
    public ?int $type = null; // AUTHOR_TYPE_HUMAN: 0, AUTHOR_TYPE_AI: 1
    public ?string $created_at = null;
}