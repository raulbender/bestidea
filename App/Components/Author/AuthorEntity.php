<?php

declare(strict_types=1);

namespace App\Components\Author;

class AuthorEntity 
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $avatar = null,
        public ?int $type = null,
        public ?string $created_at = null
    ) {}
}