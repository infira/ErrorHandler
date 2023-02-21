<?php

declare(strict_types=1);

namespace Infira\Error;

use Infira\Error\Exception\ExceptionCapsule;
use Infira\Error\Exception\TriggerException;
use Throwable;

class Error
{
    use Traits\SPLShortcuts;

    /**
     * Raise a error, code will stop executing
     *
     * @param  string  $msg
     * @param  mixed  $data  - extra data will be added to error message
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
     * @param  string|array  $name  - string, or in case of array ,every key will be added as extra data key to error output
     * @param  mixed  $data  [$name=>$data] will be added to error output
     */
    public static function setDebug(string|array $name, mixed $data = null): void
    {
        if (is_array($name) && $data === null) {
            DebugCollector::set($name);
        }
        else {
            DebugCollector::set($name, $data);
        }
    }

    public static function capsule(callable $callback, string $capsuleName = null): mixed
    {
        $capsule = new Capsule($capsuleName);
        try {
            // DebugCollector::makeCapsule($capsuleID);
            $output = $callback($capsule);
        }
//        catch (ExceptionCapsule $capsuleException) {
////            debug([
////                "ExceptionCapsule catch:" => [
////                    'file' => $capsuleException->getFile(),
////                    'line' => $capsuleException->getLine(),
////                    'trace' => $capsuleException->getTrace(),
////                ]
////            ]);
//            $capsuleException->getCapsule()->merge($capsule);
//            throw $capsuleException;
//        }
        catch (Throwable $exception) {
//            debug([
//                "Throwable catch:".$exception::class => [
//                    'file' => $exception->getFile(),
//                    'line' => $exception->getLine(),
//                    'trace' => $exception->getTrace(),
//                ]
//            ]);
            if ($exception instanceof ExceptionCapsule) {
                $capsule = $capsule->mergeParent($exception->getCapsule());
                $exception = $exception->getPrevious();
            }
            throw new ExceptionCapsule(
                $exception,
                $capsule
            );
        }
        finally {
            //debug("finally is runned");
            //DebugCollector::clearCapsule($capsuleID, $activeCapsuleID);
        }

        return $output;
    }
}