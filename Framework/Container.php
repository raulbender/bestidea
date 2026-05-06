<?php

declare(strict_types=1);

namespace Framework;

use Exception;
use Framework\Http\ScopedService;
use ReflectionClass;

/**
 * Cérebro do Framework (Injeção de Dependência).
 * DICA PARA IA:
 * 1. NUNCA use 'new Class()' para Services, Controllers ou Repositories. Use 'Container::resolve(Class::class)'.
 * 2. O Container resolve dependências do construtor automaticamente via Reflexão.
 */
class Container {
    /** @var array<string,string|callable>     */
    private static array $bindings = [];

    /** @var array<string,object> */
    private static array $instances = [];

    /** @var array<string,object> */
    private static array $requestInstances = [];

    public static Config $config;


    /**
     * Define a configuração global. Acessível via Container::$config.
     */
    public static function set(Config $config): void {
        self::$config = $config;
    }


    /**
     * @param string $abstract
     * @param string|object $concrete // Mudamos de callable para object
     */
    public static function bind(string $abstract, string|object $concrete): void {
        if (is_object($concrete)) {
            // Se for um objeto (como a Request), já registramos no balde certo agora!
            self::saveInstance($abstract, $concrete);
            return;
        }
        self::$bindings[$abstract] = $concrete;
    }

    /**
     * Resolve uma instância. Se houver dependências no construtor, elas são resolvidas recursivamente.
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public static function resolve(string $class): object {
        if (in_array($class, self::$bindings) && !isset(self::$bindings[$class])) {
            throw new Exception("Arquitetura está Violada: Use a Interface para resolver '{$class}'.");
        }

        if (isset(self::$instances[$class])) {
            /** @var T */
            return self::$instances[$class];
        }

        if (isset(self::$requestInstances[$class])) {
            /** @var T */
            return self::$requestInstances[$class];
        }

        $cacheKey = $class;

        if (isset(self::$bindings[$class])) {
            $class = self::$bindings[$class];
        }
        $reflector = new ReflectionClass($class);

        if (! $reflector->isInstantiable()) {
            throw new Exception("Class {$class} cannot be instantiated.");
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            /** @var T $instance */
            $instance = new $class();

            return self::saveInstance($cacheKey, $instance);
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if (! $type instanceof \ReflectionNamedType) {
                throw new \Exception("Parameter {$parameter->getName()} must have a named type in {$class}.");
            }

            if (! $type->isBuiltin()) {
                $typeName = $type->getName();

                /** @var class-string<object> $typeName */
                $dependencies[] = self::resolve($typeName);

                continue;
            }

            if (! $parameter->isDefaultValueAvailable()) {
                throw new Exception("I can't resolve the scalar parameter '{$parameter->getName()}' in {$class}.");
            }
            $dependencies[] = $parameter->getDefaultValue();
        }

        $instance = $reflector->newInstanceArgs($dependencies);

        /** @var T $instance */
        return self::saveInstance($cacheKey, $instance);
    }


    /**
     * Remove todas as instâncias.
     */
    public static function clearInstances(): void {
        self::$instances = [];
    }


    /**
     * A Catraca: Decide em qual balde o objeto vai morar.
     * @template T of object
     * @param class-string<T> $class
     * @param T $instance
     * @return T
     */
    private static function saveInstance(string $class, object $instance): object {
        // O "instanceof" é extremamente veloz no PHP 8
        if ($instance instanceof ScopedService) {
            return self::$requestInstances[$class] = $instance;
        }

        return self::$instances[$class] = $instance;
    }


    /**
     * Limpeza rápida: apenas o balde volátil é zerado.
     * As conexões pesadas de DB e Log continuam vivas no outro balde.
     */
    public static function clearRequestInstances(): void {
        self::$requestInstances = [];
    }
}
