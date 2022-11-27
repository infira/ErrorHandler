<?php

declare(strict_types=1);

namespace Infira\Error;

use Infira\Error\Exception\Exception;
use Ramsey\Uuid\Uuid;

class Error
{
    use Traits\CommonExceptions;
    use Traits\LogicExceptions;
    use Traits\RunTimeExceptions;

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
        throw (new Exception($msg))->width($data);
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

    public static function capsule(callable $callback): mixed
    {
        $activeCapsuleID = DebugCollector::getCapsuleID();
        $capsuleID = Uuid::uuid4()->toString();
        DebugCollector::setCapsuleID($capsuleID);
        $output = $callback();
        DebugCollector::clearCapsule($capsuleID, $activeCapsuleID);

        return $output;
    }
}