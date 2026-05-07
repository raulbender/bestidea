<?php

declare(strict_types=1);

namespace Framework\Console;

use App\Console\Commands\SeedCommand;
use Framework\Console\Commands\MakeContext;
use Framework\Console\Commands\MakeContextTotal;
use Framework\Console\Commands\MakeController;
use Framework\Console\Commands\MakeMigration;
use Framework\Console\Commands\Migrate;
use Framework\Console\Commands\Rollback;
use Framework\Console\Commands\TestMail;
use Framework\Console\Commands\MakeContextCss;
use Framework\Container;

class Kernel
{
    /** @var array<string, class-string<CommandInterface>> */
    private array $commands = [];

    public function __construct(private Container $container)
    {
        $this->commands = [
            'make:controller' => MakeController::class,
            'make:migration' => MakeMigration::class,
            'migrate' => Migrate::class,
            'rollback' => Rollback::class,
            'test:mail' => TestMail::class,
            'make:context' => MakeContext::class,
            'make:total' => MakeContextTotal::class,
            'make:csscontext' => MakeContextCss::class,
            'seed' => SeedCommand::class,
        ];
    }

    /** @param array<int, string> $argv */
    public function handle(array $argv): void
    {
        $commandName = $argv[1] ?? 'help';

        if ($commandName === 'help') {
            $this->showHelp();

            return;
        }

        if (! isset($this->commands[$commandName])) {
            echo "❌ Comando '{$commandName}' não encontrado, marujo!\n";
            echo "Digite 'php volt help' para ver os comandos disponíveis.\n";
            exit(1);
        }

        $commandClass = $this->commands[$commandName];

        /** @var CommandInterface $command */
        $command = $this->container->resolve($commandClass);

        $args = array_slice($argv, 2);

        $command->execute($args);
    }

    private function showHelp(): void
    {
        echo "🌊 Volt FPM Console \n\n";
        echo "Comandos disponíveis:\n";

        foreach ($this->commands as $name => $class) {
            /** @var CommandInterface $instance */
            $instance = $this->container->resolve($class);
            echo "  \033[32m{$name}\033[0m - " . $instance->getDescription() . "\n";
        }
    }
}
