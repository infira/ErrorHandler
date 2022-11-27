<?php

namespace Infira\Error\Traits;

use Infira\Error\Exception\{OutOfBoundsException, OverflowException, RangeException, RuntimeException, UnderflowException, UnexpectedValueException,};
use Throwable;

trait RunTimeExceptions
{

    //region constructors
    public static function getRuntimeException(string $message, int $code = 0, Throwable $previous = null): RuntimeException
    {
        return new RuntimeException($message, $code, $previous);
    }

    public static function getOutOfBoundsException(string $message, int $code = 0, Throwable $previous = null): OutOfBoundsException
    {
        return new OutOfBoundsException($message, $code, $previous);
    }

    public static function getOverflowException(string $message, int $code = 0, Throwable $previous = null): OverflowException
    {
        return new OverflowException($message, $code, $previous);
    }

    public static function getRangeException(string $message, int $code = 0, Throwable $previous = null): RangeException
    {
        return new RangeException($message, $code, $previous);
    }

    public static function getUnderflowException(string $message, int $code = 0, Throwable $previous = null): UnderflowException
    {
        return new UnderflowException($message, $code, $previous);
    }

    public static function getUnexpectedValueException(string $message, int $code = 0, Throwable $previous = null): UnexpectedValueException
    {
        return new UnexpectedValueException($message, $code, $previous);
    }
    //endregion

    //region throwers
    public static function throwRuntimeException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getRuntimeException($message, $code, $previous);
    }

    public static function throwOutOfBoundsException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getOutOfBoundsException($message, $code, $previous);
    }

    public static function throwOverflowException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getOverflowException($message, $code, $previous);
    }

    public static function throwRangeException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getRangeException($message, $code, $previous);
    }

    public static function throwUnderflowException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getUnderflowException($message, $code, $previous);
    }

    public static function throwUnexpectedValueException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getUnexpectedValueException($message, $code, $previous);
    }
    //endregion
}