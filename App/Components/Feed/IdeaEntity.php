<?php

declare(strict_types=1);

namespace App\Components\Feed;

class IdeaEntity
{
    public ?int $id = null;
    public ?int $author_id = null;
    public ?string $content = null;
    public ?string $created_at = null;

    // Campos auxiliares para o JOIN (não pertencem à tabela ideas diretamente)
    public ?string $author_name = null;
    public ?string $author_avatar = null;
    public ?array $comments = [];
}