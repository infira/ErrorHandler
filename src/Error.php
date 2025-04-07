<?php

declare(strict_types=1);

namespace Infira\Error;

use Infira\Error\Exception\ExceptionCapsule;
use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\TriggerException;
use Throwable;

class Error
{
    use Traits\SPLShortcuts;

    /**
     * Raise a error, code will stop executing
     *
     * @param string $msg
     * @param mixed $data - extra data will be added to error message
     * @return void
     * @throws TriggerException
     */
    public static function trigger(string $msg, mixed $data = null): void
    {
        $exception = (new TriggerException($msg));
        if ($data) {
            if (!is_array($data)) {
                $data = [$data];
            }
            $exception->with($data);
        }
        throw $exception;
    }

    public static function clearDebug(): void
    {
        DebugCollector::flush();
    }

    /**
     * Add extra to error output for more extended information
     *
     * @param string|array $name - string, or in case of array ,every key will be added as extra data key to error output
     * @param mixed $data [$name=>$data] will be added to error output
     */
    public static function setDebug(string|array $name, mixed $data = null): void
    {
        DebugCollector::put(...func_get_args());
    }

    /**
     * @param callable $callback
     * @param string|null $capsuleName
     * @return mixed
     * @deprecated use Error::try instead
     */
    public static function capsule(callable $callback, string $capsuleName = null): mixed
    {
        return self::try($callback, $capsuleName);
    }

    /**
     * @param callable $callback
     * @param string|Capsule|callable|array|null $capsule - in case of array debug data will be added to capsule
     * @return mixed
     */
    public static function try(callable $callback, string|Capsule|array|callable $capsule = null): mixed
    {
        if (is_array($capsule)) {
            $data = $capsule;
            $capsule = new Capsule();
            $capsule->put($data);
        }
        else if (is_callable($capsule)) {
            $callable = $capsule;
            $capsule = new Capsule();
            $capsule->onCatch($callable);
        }
        else if (is_string($capsule) || $capsule === null) {
            $capsule = new Capsule($capsule);
        }

        DebugCollector::addCapsule($capsule);
        try {
            ExceptionDataStack::__setErrorClassFileLocation(__FILE__, __LINE__ + 1);
            $output = $callback($capsule);
        }
        catch (Throwable $exception) {
            $capsule->setTrace(debug_backtrace() ?? []);
            if ($exception instanceof ThrowableDebugDataContract) {
                $capsule->pushTo('debugData', $exception->getDebugData());
                $exception->clearDebugData();
            }
            $capsule->executeOnCatch($exception);
            if ($exception instanceof ExceptionCapsule) {
                $exceptionCapsule = $exception->getCapsule();
                $exceptionCapsule->addParent($capsule);
                $capsule = $exceptionCapsule;
                $exception = $exception->getCaughtException();
            }
            throw new ExceptionCapsule(
                $exception,
                $capsule
            );
        }
        finally {
            DebugCollector::clearLastCapsule();
        }
        return $output;
    }
}