<?php

declare(strict_types=1);

namespace App\Components\Room;

interface RoomServiceInterface {
    public function createRoom(string $description): string;
    public function getRoomByUuid(string $uuid): ?RoomEntity;
}


