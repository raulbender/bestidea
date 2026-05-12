<?php

declare(strict_types=1);

namespace App\Components\Author;

use Framework\Database\DatabaseInterface;

class AuthorRepository implements AuthorRepositoryInterface {

    public function __construct(private DatabaseInterface $db) {
    }

    public function getAuthorById(int $id): ?AuthorEntity {

        $result = $this->db->table('authors')
            ->select('*')
            ->where('id', '=',$id)
            ->get();

        if (!$result) {
            return null;
        }

        $data = (array) $result[0];

        return new AuthorEntity(
            id: (int) $data['id'],
            name: $data['name'],
            avatar: $data['avatar'],
            created_at: $data['created_at']
        );
    }

    public function getRandomAuthor(): ?AuthorEntity {
        $results = $this->db->table('authors')
            ->select('*')
            ->where('type', '=', 0) // Apenas autores do tipo 0 (animal)            
            ->limit(1)
            ->orderByRaw('RAND()') // Função nativa para aleatoriedade
            ->get();

        if (empty($results)) {
            return null;
        }

        $data = (array) $results[0];

        return new AuthorEntity(
            id: (int) $data['id'],
            name: $data['name'],
            avatar: $data['avatar'],
            created_at: $data['created_at']
        );
    }
}
