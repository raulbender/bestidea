<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;

class MakeController implements CommandInterface
{
    public function getDescription(): string
    {
        return "Gera um novo Controller (Uso: make:controller NomeDoModulo/NomeDoController)";
    }

    /** @param array<int, string> $args */
    public function execute(array $args): void
    {
        if (empty($args)) {
            echo "❌ Informe o caminho do controller. Ex: Home/User\n";

            return;
        }

        $path = $args[0]; // Ex: Home/User
        $parts = explode('/', $path);

        $name = array_pop($parts); // User
        $module = implode('\\', $parts); // Home
        $folderPath = __DIR__ . "/../../../App/Components/" . implode('/', $parts);

        if (! is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $stub = file_get_contents(__DIR__ . '/../Stubs/controller.stub');
        $content = str_replace(['{{name}}', '{{module}}'], [$name, $module], ensureString($stub));

        $filePath = "{$folderPath}/{$name}Controller.php";

        if (file_exists($filePath)) {
            echo "⚠️  O controller {$name}Controller já existe!\n";

            return;
        }

        file_put_contents($filePath, $content);
        echo "✅ Controller criado com sucesso em: {$filePath}\n";
    }
}
