<?php

namespace Infira\Error\Traits;

use Infira\Error\Exception\RunTime\{
    RuntimeException,
    OutOfBoundsException,
    OverflowException,
    RangeException,
    UnderflowException,
    UnexpectedValueException,
};
use Throwable;

trait RunTimeExceptions
{
    public static function RuntimeException(string $message, int $code = 0, Throwable $previous = null): RuntimeException
    {
        return new RuntimeException($message, $code, $previous);
    }

    public static function OutOfBoundsException(string $message, int $code = 0, Throwable $previous = null): OutOfBoundsException
    {
        return new OutOfBoundsException($message, $code, $previous);
    }

    public static function OverflowException(string $message, int $code = 0, Throwable $previous = null): OverflowException
    {
        return new OverflowException($message, $code, $previous);
    }

    public static function RangeException(string $message, int $code = 0, Throwable $previous = null): RangeException
    {
        return new RangeException($message, $code, $previous);
    }

    public static function UnderflowException(string $message, int $code = 0, Throwable $previous = null): UnderflowException
    {
        return new UnderflowException($message, $code, $previous);
    }

    public static function UnexpectedValueException(string $message, int $code = 0, Throwable $previous = null): UnexpectedValueException
    {
        return new UnexpectedValueException($message, $code, $previous);
    }
}