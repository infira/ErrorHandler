<?php

declare(strict_types=1);

namespace Infira\Error;

use JetBrains\PhpStorm\ArrayShape;
use Throwable;

/**
 * This class handles users and php errors
 */
class Handler
{
    public static string $dateFormat = 'Y-m-d H:i:s';

    /**
     * @param  array  $options
     * @return void
     * @see https://www.php.net/manual/en/function.error-reporting.php
     */
    public static function register(
        #[ArrayShape([
            'errorLevel' => 'int', //defaults to
            'dateFormat' => 'string' //defaults to
        ])] array $options = []
    ): void {
        $errorLevel = $options['errorLevel'] ?? -1;
        ini_set('error_reporting', $errorLevel);
        error_reporting($errorLevel);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        static::$dateFormat = $options['dateFormat'] ?? 'Y-m-d H:i:s';
        set_error_handler(static function (int $code, string $msg, string $file = null, int $line = null) {
            throw new \ErrorException($msg, $code, 1, $file, $line);
        });
    }

    /**
     * @param  Throwable  $exception
     * @param  int  $traceOptions  https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
     * @return ExceptionDataStack
     */
    public static function compile(Throwable $exception, int $traceOptions = DEBUG_BACKTRACE_IGNORE_ARGS): ExceptionDataStack
    {
        return new ExceptionDataStack($exception, $traceOptions, DebugCollector::getActiveCapsuleID());
    }
}