<?php

declare(strict_types=1);

namespace Framework\Database;

interface MigrationInterface
{
    public function up(DatabaseInterface $db): void;
    public function down(DatabaseInterface $db): void;
}
