<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MakeContextCss implements CommandInterface
{
    public function getDescription(): string
    {
        return "Gera o context_ai_css.xml com o conteúdo integral do design system Volt CSS";
    }

    public function execute(array $args): void
    {
        $xml = "<css_context>" . PHP_EOL;
        // Caminho base onde residem os estilos do Volt
        $basePath = (string) realpath(__DIR__ . '/../../../public/css/volt/');

        if ($basePath === '') {
            echo "❌ Erro: Caminho do CSS não encontrado.\n";
            return;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'css') {
                continue;
            }

            // Define o caminho relativo para facilitar a leitura da IA (ex: public/css/volt/core/colors.css)
            $relativePath = str_replace((string) realpath($basePath . '/../../../../') . '/', '', $file->getPathname());
            
            echo "🎨 Mapeando Estilo: {$relativePath}\n";

            // Lemos o conteúdo total, sem filtros, para garantir a precisão do Design System
            $content = (string) file_get_contents($file->getPathname());

            $xml .= "  <file path=\"{$relativePath}\">" . PHP_EOL;
            $xml .= "    <![CDATA[" . PHP_EOL . $content . PHP_EOL . "    ]]>" . PHP_EOL;
            $xml .= "  </file>" . PHP_EOL;
        }

        $xml .= "</css_context>";

        // Salva o manifesto visual na raiz do projeto
        file_put_contents(dirname($basePath, 3) . '/context_ai_css.xml', $xml);
        
        echo "\n✨ O Manifesto Visual Total foi gerado: context_ai_css.xml\n";
    }
}