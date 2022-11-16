<?php

namespace Infira\Error\Traits;

use Infira\Error\Exception\Logic\{
    LogicException,
    BadFunctionCallException,
    BadMethodCallException,
    DomainException,
    InvalidArgumentException,
    LengthException,
    OutOfRangeException,
};
use Throwable;

trait LogicExceptions
{
    public static function LogicException(string $message, int $code = 0, Throwable $previous = null): LogicException
    {
        return new LogicException($message, $code, $previous);
    }

    public static function BadFunctionCallException(string $message, int $code = 0, Throwable $previous = null): BadFunctionCallException
    {
        return new BadFunctionCallException($message, $code, $previous);
    }

    public static function BadMethodCallException(string $message, int $code = 0, Throwable $previous = null): BadMethodCallException
    {
        return new BadMethodCallException($message, $code, $previous);
    }

    public static function DomainException(string $message, int $code = 0, Throwable $previous = null): DomainException
    {
        return new DomainException($message, $code, $previous);
    }

    public static function InvalidArgumentException(string $message, int $code = 0, Throwable $previous = null): InvalidArgumentException
    {
        return new InvalidArgumentException($message, $code, $previous);
    }

    public static function LengthException(string $message, int $code = 0, Throwable $previous = null): LengthException
    {
        return new LengthException($message, $code, $previous);
    }

    public static function OutOfRangeException(string $message, int $code = 0, Throwable $previous = null): OutOfRangeException
    {
        return new OutOfRangeException($message, $code, $previous);
    }
}