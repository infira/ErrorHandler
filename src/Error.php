<?php

declare(strict_types=1);

namespace Infira\Error;

use Infira\Error\Traits\LogicExceptions;
use Infira\Error\Traits\RunTimeExceptions;
use Ramsey\Uuid\Uuid;

class Error
{
    use RunTimeExceptions;
    use LogicExceptions;

    /**
     * Raise a error, code will stop executing
     *
     * @param  string  $msg
     * @param  mixed  $data  - extra data will be added to error message
     * @return void
     * @throws Exception
     */
    public static function trigger(string $msg, mixed $data = null): void
    {
        throw new Exception($msg, $data);
    }

    public static function clearDebug(): void
    {
        DebugCollector::flush();
    }

    /**
     * Add extra to error output for more extended information
     *
     * @param  string|array  $name  - string, or in case of array ,every key will be added as extra data key to error output
     * @param  mixed  $data  [$name=>$data] will be added to error output
     */
    public static function setDebug(string|array $name, mixed $data = null): void
    {
        if (is_array($name) && $data === null) {
            foreach ($name as $n => $v) {
                self::setDebug($n, $v);
            }
        }
        else {
            DebugCollector::set($name, $data);
        }
    }

    public static function capsule(callable $callback, mixed...$params): mixed
    {
        $capsuleID = Uuid::uuid4()->toString();
        DebugCollector::setCapsuleID($capsuleID);
        $output = $callback(...$params);
        DebugCollector::clearCapsule($capsuleID);

        return $output;
    }
}