<?php

declare(strict_types=1);

namespace Framework\Extensions\AI;

interface AiClientInterface
{
    /** @param array<string, mixed> $payload */
    public function generateResponse(array $payload): ?string;
}
