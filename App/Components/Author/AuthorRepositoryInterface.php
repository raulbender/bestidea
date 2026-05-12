<?php

declare(strict_types=1);

namespace App\Components\Author;

interface AuthorRepositoryInterface {
    public function getAuthorById(int $id): ?AuthorEntity;
    public function getRandomAuthor(): ?AuthorEntity;
}