<?php

declare(strict_types=1);

namespace App\Components\Room;

use Framework\Database\DatabaseInterface;

class RoomRepository implements RoomRepositoryInterface {
    public function __construct(private DatabaseInterface $db) {
    }

    public function create(RoomEntity $room): bool {
        return $this->db->insert("rooms", $room);
    }

    public function findByUuid(string $uuid): ?RoomEntity {
        $results = $this->db->table('rooms')
            ->select('*')
            ->where('uuid', '=', $uuid)
            ->limit(1)
            ->get(); // Pegamos como stdClass para mapear na mão com segurança


        if (empty($results)) {
            return null;
        }

        $data = (array) $results[0];

        return new RoomEntity(
            uuid: $data['uuid'],
            description: $data['description'],
            expires_at: $data['expires_at'], 
            id: (int) $data['id'],
            created_at: $data['created_at']  
        );
    }
}
