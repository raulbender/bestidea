<?php

namespace App\Components\Room;

class RoomService implements RoomServiceInterface {
    public function __construct(private RoomRepositoryInterface $roomRepository) {
    }


    public function createRoom(string $description): string {
        $uuid = bin2hex(random_bytes(16));

        $expiresAt = (new \DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');

        $room = new RoomEntity(
            uuid: $uuid,
            description: $description,
            expires_at: $expiresAt
        );

        $success = $this->roomRepository->create($room);

        if (!$success) {
            throw new \RuntimeException("Erro crítico: Não foi possível criar a sala no banco de dados.");
        }

        return $uuid;
    }


    public function getRoomByUuid(string $uuid): ?RoomEntity {
        $room = $this->roomRepository->findByUuid($uuid);

        if (!$room) {
            return null;
        }

        // Opcional: Validar se a sala já expirou (comparando expiresAt com o "now")
        $now = new \DateTime();
        $expiration = new \DateTime($room->expires_at);

        if ($now > $expiration) {
            return null; // Sala expirada
        }

        return $room;
    }
}
