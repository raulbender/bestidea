<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Reflection;
use ReflectionClass;

class MakeContextTotal implements CommandInterface
{
    public function getDescription(): string
    {
        return "Gera o context_total.xml varrendo todo o projeto com a lógica de Reflection oficial";
    }

    public function execute(array $args): void
    {
        $xml = "<total_context>" . PHP_EOL;
        $basePath = (string) realpath(__DIR__ . '/../../../');

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($basePath . '/', '', $file->getPathname());



            if (
                str_contains($relativePath, 'vendor/') ||           // Bibliotecas externas (lixo para o contexto)
                str_contains($relativePath, 'Framework/Tests/') ||  // Testes (importantes, mas não definem a estrutura)
                str_contains($relativePath, 'storage/') ||          // LOGS, CACHE E SESSÕES (Obrigatório excluir!)
                str_contains($relativePath, 'public/') ||           // Assets, CSS, JS e imagens
                str_contains($relativePath, '.git/') ||             // Metadados do Git
                str_contains($relativePath, '.env') ||              // SEGURANÇA: Nunca envie suas chaves reais
                str_ends_with($relativePath, '.xml') ||             // Evita ler o próprio context_ai e context_total
                str_ends_with($relativePath, '.json') ||            // Composer.json e outros metas
                str_ends_with($relativePath, '.lock')               // Composer.lock
            ) {
                continue;
            }

            echo "🔍 Mapeando: {$relativePath}\n";

            $structure = $this->getStructure($file->getPathname(), $relativePath);

            $xml .= "  <file path=\"{$relativePath}\">" . PHP_EOL;
            $xml .= "    <![CDATA[" . PHP_EOL . $structure . PHP_EOL . "    ]]>" . PHP_EOL;
            $xml .= "  </file>" . PHP_EOL;
        }

        $xml .= "</total_context>";

        file_put_contents($basePath . '/context_total.xml', $xml);
        echo "\n🏁 O Grande Manifesto Total foi gerado: context_total.xml\n";
    }

    private function getStructure(string $fullPath, string $relativePath): string
    {
        // Se for arquivo procedural, usamos Regex.
        if (str_ends_with($relativePath, 'boot.php') || str_ends_with($relativePath, 'worker.php') || str_ends_with($relativePath, 'Helpers.php')) {
            return $this->extractScriptSkeleton($fullPath);
        }

        // Converte o caminho para Namespace (Ex: Framework/Http/Request.php -> Framework\Http\Request)
        $className = str_replace(['/', '.php'], ['\\', ''], $relativePath);

        // Verifica se a classe/interface/trait existe para refletir
        if (class_exists($className) || interface_exists($className) || trait_exists($className)) {
            return $this->reflectClass($className);
        }

        // Se não for uma classe carregável, tenta extrair o esqueleto via Regex
        return $this->extractScriptSkeleton($fullPath);
    }

    private function reflectClass(string $className): string
    {
        try {
            $reflection = new ReflectionClass($className);
            $output = "namespace " . (string) $reflection->getNamespaceName() . ";" . PHP_EOL . PHP_EOL;

            // DocBlock da Classe
            $classDoc = (string) $reflection->getDocComment();
            if ($classDoc !== '') {
                $output .= $classDoc . PHP_EOL;
            }

            $type = $reflection->isInterface() ? "interface" : ($reflection->isTrait() ? "trait" : "class");
            $output .= "{$type} " . (string) $reflection->getShortName() . PHP_EOL . "{" . PHP_EOL;

            // Propriedades
            foreach ($reflection->getProperties() as $prop) {
                $modifiers = implode(' ', Reflection::getModifierNames($prop->getModifiers()));
                $propType = $prop->hasType() ? (string) $prop->getType() . " " : "";
                $output .= "    {$modifiers} {$propType}\${$prop->getName()};" . PHP_EOL;
            }

            // Métodos
            foreach ($reflection->getMethods() as $method) {
                // Pega o DocBlock do método (Importante para o PHPStan Level 9)
                $methodDoc = (string) $method->getDocComment();
                if ($methodDoc !== '') {
                    $output .= PHP_EOL . "    " . $methodDoc . PHP_EOL;
                }

                $modifiers = implode(' ', Reflection::getModifierNames($method->getModifiers()));

                $params = [];
                foreach ($method->getParameters() as $param) {
                    $pType = $param->hasType() ? (string) $param->getType() . " " : "";
                    $default = $param->isDefaultValueAvailable()
                        ? " = " . var_export($param->getDefaultValue(), true)
                        : "";
                    $params[] = "{$pType}\${$param->getName()}{$default}";
                }

                $retType = $method->hasReturnType() ? ": " . (string) $method->getReturnType() : "";

                $output .= "    {$modifiers} function {$method->getName()}(" . implode(', ', $params) . "){$retType} { /* ... */ }" . PHP_EOL;
            }

            $output .= "}";

            return $output;
        } catch (\Throwable $e) {
            return "// Erro ao refletir {$className}: " . $e->getMessage();
        }
    }

    private function extractScriptSkeleton(string $filePath): string
    {
        if (! file_exists($filePath)) {
            return "// Arquivo não encontrado";
        }
        $content = (string) file_get_contents($filePath);

        // Mantém apenas a carcaça das funções
        return (string) preg_replace('/\{(?:[^{}]|(?R))*\}/', '{ /* ... */ }', $content);
    }
}
