<?php

namespace Infira\Error\Traits;

use Infira\Error\Exception\{ErrorException, Exception};
use Throwable;

trait CommonExceptions
{

    //region constructors
    public static function getException(string $message, int $code = 0, Throwable $previous = null): Exception
    {
        return new Exception($message, $code, $previous);
    }

    public static function getErrorException(
        string $message,
        int $code = 0,
        int $severity = 1,
        string|null $filename = __FILE__,
        int|null $line = __LINE__,
        Throwable $previous = null
    ): ErrorException {
        return new ErrorException($message, $code, $severity, $filename, $line, $previous);
    }
    //endregion

    //region throwers
    public static function throwException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getException($message, $code, $previous);
    }

    public static function throwErrorException(
        string $message,
        int $code = 0,
        int $severity = 1,
        string|null $filename = __FILE__,
        int|null $line = __LINE__,
        Throwable $previous = null
    ): void {
        throw self::getErrorException($message, $code, $severity, $filename, $line, $previous);
    }

    //endregion
}