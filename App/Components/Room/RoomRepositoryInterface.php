<?php

namespace App\Components\Room;

use Framework\Database\DatabaseInterface;

interface RoomRepositoryInterface {
    public function create(RoomEntity $room): bool;
    public function findByUuid(string $uuid): ?RoomEntity;
}