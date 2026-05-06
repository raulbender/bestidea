<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;
use Framework\Database\MigrationService;

class Migrate implements CommandInterface
{
    public function __construct(private MigrationService $migrationService)
    {
    }

    public function getDescription(): string
    {
        return "Roda todas as migrations pendentes no banco de dados.";
    }

    /** @param array<int, string> $args */
    public function execute(array $args): void
    {
        echo "🌊 Volt FPM: Iniciando a navegação das migrations...\n";

        try {
            $this->migrationService->run();
        } catch (\Exception $e) {
            echo "❌ Erro durante a execução: " . $e->getMessage() . "\n";
        }
    }
}
