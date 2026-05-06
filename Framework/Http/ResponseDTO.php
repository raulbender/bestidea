<?php

declare(strict_types=1);

namespace Framework\Http;

class ResponseDTO {
    public function __construct(
        public int $statusCode = 200,
        public array $headers = [],
        public string $body = '',
    ) {}
}