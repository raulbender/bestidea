<?php

declare(strict_types=1);

namespace Framework\Database;

use Framework\Utils\Logger\Logger;

class MigrationService
{
    private string $migrationsPath = __DIR__ . '/../../App/Database/Migrations/';

    public function __construct(private DatabaseInterface $db)
    {
    }

    public function run(): void
    {
        $this->createMigrationsTable();

        $executedMigrations = $this->getExecutedMigrations();

        $availableMigrations = $this->getAvailableMigrations();

        $newMigrations = array_diff($availableMigrations, $executedMigrations);

        if (empty($newMigrations)) {
            echo "⚓ Todas as migrations já foram aplicadas. O banco está atualizado!\n";

            return;
        }

        foreach ($newMigrations as $migration) {
            $this->applyMigration($migration);
        }

        echo "✅ Processo finalizado com sucesso.\n";
    }

    /** @return array<int, string> */
    private function getExecutedMigrations(): array
    {
        $stmt = $this->db->query("SELECT migration_name FROM migrations");

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /** @return array<int, string> */
    private function getAvailableMigrations(): array
    {
        if (! is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0777, true);
        }

        $files = scandir($this->migrationsPath) ?: [];
        $migrations = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $migrations[] = pathinfo($file, PATHINFO_FILENAME); // Pega o nome sem o .php
            }
        }

        sort($migrations); // Garante a ordem alfabética/cronológica

        return $migrations;
    }

    private function applyMigration(string $migrationName): void
    {
        echo "🚀 Aplicando: {$migrationName}...\n";

        require_once $this->migrationsPath . $migrationName . '.php';

        $className = "App\\Database\\Migrations\\" . $migrationName;

        /** @var MigrationInterface $migration */
        $migration = new $className();

        echo "   -> Rodando o método UP...\n";
        $migration->up($this->db);

        echo "   -> Anotando no diário de bordo...\n";
        $this->db->query(
            "INSERT INTO migrations (migration_name) VALUES (:name)",
            ['name' => $migrationName]
        );

        echo "✅ Concluído!\n\n";
    }


    public function rollback(): void
    {
        $stmt = $this->db->query("SELECT migration_name FROM migrations ORDER BY id DESC LIMIT 1");
        $lastMigration = $stmt->fetchColumn();

        if (! $lastMigration) {
            echo "⚓ Nada para reverter. O diário de bordo está vazio!\n";

            return;
        }

        echo "⏪ Revertendo: {$lastMigration}...\n";

        require_once $this->migrationsPath . $lastMigration . '.php';
        $className = "App\\Database\\Migrations\\" . $lastMigration;

        $migration = new $className();

        /** @var MigrationInterface $migration */
        $migration->down($this->db);

        $this->db->query("DELETE FROM migrations WHERE migration_name = :name", ['name' => $lastMigration]);

        echo "✅ Reversão concluída com sucesso.\n";
    }


    private function createMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration_name VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->db->query($sql);
        Logger::info("Tabela de migrations verificada com sucesso.");
    }
}
