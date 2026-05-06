<?php

declare(strict_types=1);

namespace Framework\Utils\Error;

readonly class ErrorResponse
{
    public function __construct(
        public int $status,
        public string $html
    ) {}
}