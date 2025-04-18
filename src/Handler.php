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
     * @param array $options
     * @return void
     * @see https://www.php.net/manual/en/function.error-reporting.php
     */
    public static function register(
        #[ArrayShape([
            'errorLevel' => 'int', //defaults to E_ALL
            'dateFormat' => 'string' //defaults to
        ])] array $options = []
    ): void {
        $errorLevel = $options['errorLevel'] ?? E_ALL;
        error_reporting($errorLevel);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        static::$dateFormat = $options['dateFormat'] ?? 'Y-m-d H:i:s';
        set_error_handler(static function (int $code, string $msg, string $file = null, int $line = null) {
            throw new \ErrorException($msg, $code, 1, $file, $line);
        }, $errorLevel);
    }

    /**
     * @param Throwable $exception
     * @param array{
     *      ignoreArgs: bool,
     *      voidInternalFiles: bool,
     *      setArgumentNames: bool,
     *      shortCallable: bool,
     *  } $traceOptions
     * @return ExceptionDataStack
     */
    public static function compile(Throwable $exception, array $traceOptions = []): ExceptionDataStack
    {
        return new ExceptionDataStack($exception, $traceOptions);
    }
}