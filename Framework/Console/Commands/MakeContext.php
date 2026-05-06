<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;
use Reflection;
use ReflectionClass;

class MakeContext implements CommandInterface
{
    private const CORE_FILES = [
        'Framework/Container.php',
        'Framework/Config.php',
        'Framework/Http/Request.php',
        'Framework/BaseController.php',
        'Framework/Database/DatabaseInterface.php',
        'Framework/Database/QueryBuilder.php',
        'Framework/Http/Session/SessionInterface.php',
        'Framework/Utils/Navigation.php',
        'Framework/Utils/Helpers.php',
        'Framework/Utils/Logger/Logger.php',
        'Framework/Utils/Error/ErrorHandler.php',
        'Framework/Http/Kernel.php',
        'Framework/BaseRoute.php',
        'worker.php',
    ];

    public function getDescription(): string
    {
        return "Gera o context_ai.xml com assinaturas, DocBlocks e diretrizes de IA";
    }

    /** @param array<int, string> $args */
    public function execute(array $args): void
    {
        $xml = "<context>" . PHP_EOL;
        $basePath = (string) realpath(__DIR__ . '/../../../');

        foreach (self::CORE_FILES as $file) {
            $fullPath = (string) realpath($basePath . '/' . $file);

            if ($fullPath !== '' && file_exists($fullPath)) {
                $structure = $this->getStructure($fullPath, $file);

                $xml .= "  <file path=\"{$file}\">" . PHP_EOL;
                $xml .= "    <![CDATA[" . PHP_EOL . $structure . PHP_EOL . "    ]]>" . PHP_EOL;
                $xml .= "  </file>" . PHP_EOL;

                echo "✅ Mapeado: {$file}\n";
            }
        }

        $xml .= "  <instructions>" . PHP_EOL;
        $xml .= "    <![CDATA[" . PHP_EOL;
        $xml .= "    Utilize as ferramentas desse contexto para a geração do código." . PHP_EOL;
        $xml .= "    Lembre-se também que estamos utilizando PHPStan level 9. " . PHP_EOL;
        $xml .= "    Respeite as assinaturas e interfaces definidas neste manifesto." . PHP_EOL;
        $xml .= "    ]]>" . PHP_EOL;
        $xml .= "  </instructions>" . PHP_EOL;

        $xml .= "</context>";

        file_put_contents($basePath . '/context_ai.xml', $xml);
        echo "\n🚀 Mapa de arquitetura sênior gerado: context_ai.xml\n";
    }

    private function getStructure(string $fullPath, string $relativePath): string
    {
        if (str_ends_with($relativePath, 'boot.php') || str_ends_with($relativePath, 'worker.php') || str_ends_with($relativePath, 'Helpers.php')) {
            return $this->extractScriptSkeleton($fullPath);
        }

        $className = str_replace(['Framework/', '/', '.php'], ['Framework\\', '\\', ''], $relativePath);

        if (class_exists($className) || interface_exists($className)) {
            return $this->reflectClass($className);
        }

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

            $type = $reflection->isInterface() ? "interface" : "class";
            $output .= "{$type} " . (string) $reflection->getShortName() . PHP_EOL . "{" . PHP_EOL;

            foreach ($reflection->getProperties() as $prop) {
                $modifiers = implode(' ', Reflection::getModifierNames($prop->getModifiers()));
                $propType = $prop->hasType() ? (string) $prop->getType() . " " : "";
                $output .= "    {$modifiers} {$propType}\${$prop->getName()};" . PHP_EOL;
            }

            // Métodos (Corrigido: Definição antes do uso)
            foreach ($reflection->getMethods() as $method) {
                // 1. Pega o DocBlock do método
                $methodDoc = (string) $method->getDocComment();
                if ($methodDoc !== '') {
                    $output .= PHP_EOL . "    " . $methodDoc . PHP_EOL;
                }

                // 2. Calcula Modificadores
                $modifiers = implode(' ', Reflection::getModifierNames($method->getModifiers()));

                // 3. Processa Parâmetros
                $params = [];
                foreach ($method->getParameters() as $param) {
                    $pType = $param->hasType() ? (string) $param->getType() . " " : "";
                    $default = $param->isDefaultValueAvailable()
                        ? " = " . var_export($param->getDefaultValue(), true)
                        : "";
                    $params[] = "{$pType}\${$param->getName()}{$default}";
                }

                // 4. Retorno
                $retType = $method->hasReturnType() ? ": " . (string) $method->getReturnType() : "";

                // 5. Monta a assinatura
                $output .= "    {$modifiers} function {$method->getName()}(" . implode(', ', $params) . "){$retType} { /* ... */ }" . PHP_EOL;
            }

            $output .= "}";

            return $output;
        } catch (\Throwable $e) {
            return "// Erro ao refletir classe {$className}: " . $e->getMessage();
        }
    }

    private function extractScriptSkeleton(string $filePath): string
    {
        $content = (string) file_get_contents($filePath);

        return (string) preg_replace('/\{(?:[^{}]|(?R))*\}/', '{ /* ... */ }', $content);
    }
}
