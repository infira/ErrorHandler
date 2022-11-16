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
    public static string $basePath = '';

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
        ini_set('error_reporting', (string)$options['errorLevel']);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting($options['errorLevel']);
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        if (isset($trace[0])) {
            static::$basePath = $trace[0]['file'] ?? '';
            static::$basePath = dirname(static::$basePath);
        }
        static::$dateFormat = $options['dateFormat'] ?? 'Y-m-d H:i:s';
    }

    /**
     * @param  Throwable  $exception
     * @param  int  $debugBacktraceOption  https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
     * @return ExceptionDataStack
     */
    public static function compile(Throwable $exception, int $debugBacktraceOption = DEBUG_BACKTRACE_IGNORE_ARGS): ExceptionDataStack
    {
        $trace = $exception->getTrace();
        if (!$trace) {
            $trace = debug_backtrace($debugBacktraceOption);
        }

        return new ExceptionDataStack($exception, $trace, $debugBacktraceOption, DebugCollector::getCapsuleID());
    }
}