<?php

namespace App\Components\Room;

use App\Components\Author\AuthorRepositoryInterface;

class RoomService implements RoomServiceInterface {
    public function __construct(private RoomRepositoryInterface $roomRepository,
                                private AuthorRepositoryInterface $authorRepository) {
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


public function getRoomDTO(string $uuid, ?string $authorId): RoomDTO 
{
    $room = $this->roomRepository->findByUuid($uuid);
    if (!$room) {
        throw new \RuntimeException("Sala não encontrada.", 404);
    }

    if ($authorId) {
        $author = $this->authorRepository->getAuthorById((int)$authorId);
    } else {
        $author = $this->authorRepository->getRandomAuthor();
    }    

    return new RoomDTO(
        uuid: $room->uuid,
        description: $room->description,
        expires_at: $room->expires_at . "Z",
        author: __($author->name),
        avatar: $author->avatar,
        author_id: $author->id // Útil para o Controller carimbar o cookie
    );
}
}
