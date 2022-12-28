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
        ini_set('error_reporting', (string)$options['errorLevel']);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting($options['errorLevel']);
        static::$dateFormat = $options['dateFormat'] ?? 'Y-m-d H:i:s';

        register_shutdown_function(static function () {
            if (error_get_last()) {
                //throw new \ErrorException($msg, $code, 1, $file, $line);
                debug(["register_shutdown_function" => error_get_last()]);
                exit;
                echo 'Script executed with success', PHP_EOL;
                debug(getTrace());
                debug(error_get_last());
                exit();
            }
            exit();
        });
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
        $trace = $exception->getTrace();
        if (!$trace && !($exception instanceof \Error)) {
            debug($exception);
            debug(debug_backtrace());
            exit("ErrorHandler kas seda on vaja siia?");
            $trace = debug_backtrace();
        }

        return new ExceptionDataStack($exception, $traceOptions, DebugCollector::getActiveCapsuleID());
    }
}