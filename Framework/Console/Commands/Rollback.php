<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;
use Framework\Database\MigrationService;

class Rollback implements CommandInterface
{
    public function __construct(private MigrationService $migrationService)
    {
    }

    public function getDescription(): string
    {
        return "Reverte a última migration executada.";
    }

    /** @param array<int, string> $args */
    public function execute(array $args): void
    {
        echo "⏪ Volt FPM: Afastando da costa... Revertendo última alteração.\n";

        try {
            $this->migrationService->rollback();
        } catch (\Exception $e) {
            echo "❌ Erro ao reverter: " . $e->getMessage() . "\n";
        }
    }
}
