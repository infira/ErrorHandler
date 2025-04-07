<?php

declare(strict_types=1);

namespace Infira\Error;

class DebugCollector
{
    private static ErrorDataCollection $data;
    /**
     * @var Capsule[];
     */
    private static array $capsules = [];

    private static function callData(string $method, mixed ...$args): mixed
    {
        if (!isset(self::$data)) {
            self::$data = new ErrorDataCollection();
        }
        if (self::$capsules) {
            $capsule = array_reverse(self::$capsules)[0];
            return $capsule->$method(...$args);
        }
        return self::$data->$method(...$args);
    }

    /**
     * Add extra to error output for more extended information
     *
     * @param string|array $name - string, or in case of array ,every key will be added as extra data key to error output
     * @param mixed $data [$name=>$data] will be added to error output
     * @deprecated use put instead
     */
    public static function set(string|array $name, mixed $data = null): void
    {
        self::put(...func_get_args());
    }

    /**
     * Add extra to error output for more extended information
     *
     * @param string|array $name - string, or in case of array ,every key will be added as extra data key to error output
     * @param mixed $data [$name=>$data] will be added to error output
     */
    public static function put(string|array $name, mixed $data = null): void
    {
        self::callData('put', ...func_get_args());
    }

    public static function pushTo(string|array $to, mixed $data): void
    {
        self::callData('pushTo', ...func_get_args());
    }

    public static function push(mixed $data): void
    {
        self::callData('push', ...func_get_args());
    }

    public static function all(): array
    {
        return self::callData('all');
    }

    public static function flush(): void
    {
        self::callData('flush');
    }

    public static function addCapsule(Capsule $capsule): void
    {
        self::$capsules[] = $capsule;
    }

    public static function clearLastCapsule(): void
    {
        array_pop(self::$capsules);
    }
}