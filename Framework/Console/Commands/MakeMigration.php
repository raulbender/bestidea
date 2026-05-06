<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;

class MakeMigration implements CommandInterface
{
    public function getDescription(): string
    {
        return "Gera uma nova migration (Uso: make:migration create_users_table)";
    }

    /** @param array<int, string> $args */
    public function execute(array $args): void
    {
        if (empty($args)) {
            echo "❌ Informe o nome da migration. Ex: create_posts_table\n";

            return;
        }

        $name = $args[0];
        $timestamp = date('Y_m_d_His');
        $fileName = "m{$timestamp}_{$name}";
        $className = "m{$timestamp}_{$name}";

        $tableName = str_replace(['create_', '_table'], '', $name);

        $stub = file_get_contents(__DIR__ . '/../Stubs/migration.stub');
        $content = str_replace(['{{className}}', '{{tableName}}'], [$className, $tableName], ensureString($stub));

        $path = __DIR__ . "/../../../App/Database/Migrations/{$fileName}.php";

        file_put_contents($path, $content);

        echo "✅ Migration criada: App/Database/Migrations/{$fileName}.php\n";
    }
}
