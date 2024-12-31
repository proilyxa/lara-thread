<?php

declare(strict_types=1);

namespace Proilyxa\LaraThread;

use Exception;
use ReflectionMethod;
use Swoole\Thread;
use Swoole\Thread\ArrayList;
use Swoole\Thread\Map;
use Swoole\Thread\Queue;

class LaraThread
{
    public static string $method = 'run';

    private static array $typesForExclude = [
        ArrayList::class => 1,
        Map::class => 1,
        Queue::class => 1,
    ];

    private function __construct()
    {

    }

    public static function run(string $class, mixed ...$params): Thread
    {
        if (!method_exists($class, self::$method)) {
            throw new Exception(sprintf('Your class % must have method %s', $class, self::$method));
        }

        return new Thread(base_path('lara-thread.php'), $class, ...$params);
    }

    public static function castMethodParam(string $targetClass, array $args): array
    {
        $indexes = [];
        foreach (self::getParameterTypes($targetClass, 'run') as $key => $type) {
            if (isset(self::$typesForExclude[$type])) {
                $indexes[$key] = true;
            }
        }

        $params = [];
        foreach ($args as $key => $value) {
            $params[$key] = isset($indexes[$key]) ? $value : self::recursiveSerialize($value);
        }

        return $params;
    }

    public static function recursiveSerialize(mixed $param): mixed
    {
        if (is_array($param)) {
            $params = [];
            foreach ($param as $key => $value) {
                $params[$key] = self::recursiveSerialize($value);
            }
            return $params;
        }

        if ($param instanceof ArrayList || $param instanceof Map) {
            return self::recursiveSerialize($param->toArray());
        }

        if (!is_object($param) || $param instanceof Queue) {
            return $param;
        }

        return unserialize(serialize($param));
    }

    private static function getParameterTypes(string $className, string $methodName): array
    {
        $parameters = (new ReflectionMethod($className, $methodName))->getParameters();
        $types = [];
        foreach ($parameters as $param) {
            $types[] = $param->getType() ? $param->getType()->getName() : 'mixed';
        }

        return $types;
    }
}
