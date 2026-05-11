<?php

declare(strict_types=1);

namespace App\Components\Room;

class RoomEntity 
{
    public function __construct(
        public ?string $uuid = null,
        public ?string $description = null,
        public ?string $expires_at = null,
        public ?int $id = null,
        public ?string $created_at = null
    ) {}

 
}