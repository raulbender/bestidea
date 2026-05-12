<?php

declare(strict_types=1);

namespace App\Components\Room;

class RoomDTO 
{
    public function __construct(
        public ?string $uuid = null,
        public ?string $description = null,
        public ?string $expires_at = null,
        public ?string $author = null,
        public ?string $avatar = null,
        public ?int $author_id = null
    ) {}
}