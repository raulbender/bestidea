<?php

declare(strict_types=1);

namespace Framework\Console;

interface CommandInterface
{
    public function getDescription(): string;

    /** @param array<int, string> $args */
    public function execute(array $args): void;
}
