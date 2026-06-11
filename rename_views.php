<?php

declare(strict_types=1);

// Caminho absoluto para a pasta de views do navio
$viewsDir = __DIR__ . '/App/Views';

if (!is_dir($viewsDir)) {
    echo "❌ Erro: O diretório '$viewsDir' não foi encontrado!\n";
    exit(1);
}

echo "🚢 Iniciando a conversão de extensões na tripulação de Views...\n";

// Criamos um iterador recursivo para varrer todas as subpastas
$directoryIterator = new RecursiveDirectoryIterator($viewsDir);
$iterator = new RecursiveIteratorIterator($directoryIterator);

$convertedCount = 0;

foreach ($iterator as $file) {
    // Ignora os diretórios "." e ".."
    if ($file->isDir()) {
        continue;
    }

    // Pega o caminho completo do arquivo
    $currentPath = $file->getRealPath();
    
    // Verifica se o arquivo termina exatamente com .phtml
    if (pathinfo($currentPath, PATHINFO_EXTENSION) === 'phtml') {
        // Define o novo nome trocando a extensão para .php
        $newPath = preg_replace('/\.phtml$/', '.php', $currentPath);
        
        // Executa a renomeação no sistema de arquivos
        if (rename($currentPath, $newPath)) {
            echo "✅ Convertido: " . str_replace(__DIR__ . '/', '', $currentPath) . " ➡️ .php\n";
            $convertedCount++;
        } else {
            echo "❌ Falha ao mover: " . str_replace(__DIR__ . '/', '', $currentPath) . "\n";
        }
    }
}

echo "\n⚓ Concluído! Total de arquivos convertidos: $convertedCount\n";