<?php

declare(strict_types=1);

namespace App\Components\Feed;

interface FeedRepositoryInterface
{
    /** * Busca todas as ideias com os dados básicos dos autores.
     * @return array<int, array<string, mixed>> 
     */
    public function findAllWithAuthors(): array;

    /** * Registra uma nova ideia no diário de bordo.
     * @param IdeaEntity $idea 
     */
    public function createIdea(IdeaEntity $idea): bool;
}