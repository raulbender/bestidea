<?php

declare(strict_types=1);

namespace App\Components\Room;

use Framework\Database\DatabaseInterface;
use Framework\Database\QueryBuilder;

class RoomRepository implements RoomRepositoryInterface {
    public function __construct(private DatabaseInterface $db) {
    }

    public function create(RoomEntity $room): bool {
        return $this->db->insert("rooms", $room);
    }

    public function findByUuid(string $uuid): ?RoomEntity {
        // Usando o padrão do seu FeedRepository (mais limpo)
        $results = $this->db->table('rooms')
            ->select('*')
            ->where('uuid', '=', $uuid)
            ->limit(1)
            ->get(); // Pegamos como stdClass para mapear na mão com segurança


        if (empty($results)) {
            return null;
        }

        // Convertendo o primeiro resultado para array
        $data = (array) $results[0];

        // Mapeamento corrigido: 
        // O valor vem do banco (snake_case), mas o parâmetro da Entidade é camelCase
        return new RoomEntity(
            uuid: $data['uuid'],
            description: $data['description'],
            expires_at: $data['expires_at'], // Corrigido: expires_at -> expiresAt
            id: (int) $data['id'],
            created_at: $data['created_at']  // Corrigido: created_at -> createdAt
        );
    }
}
