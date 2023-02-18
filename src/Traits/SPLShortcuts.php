<?php

namespace Infira\Error\Traits;

use Infira\Error\Exception\SPL\{BadFunctionCallException, BadMethodCallException, DomainException, ErrorException, InvalidArgumentException, LengthException, LogicException, OutOfBoundsException, OutOfRangeException, OverflowException, RangeException, RuntimeException, UnderflowException, UnexpectedValueException};
use Infira\Error\Exception\SPL\Exception;
use Throwable;

trait SPLShortcuts
{
    public static function exception(string $message, int $code = 0, Throwable $previous = null): Exception
    {
        return new Exception($message, $code, $previous);
    }

    public static function errorException(
        string $message,
        int $code = 0,
        int $severity = 1,
        string|null $filename = __FILE__,
        int|null $line = __LINE__,
        Throwable $previous = null
    ): ErrorException {
        return new ErrorException($message, $code, $severity, $filename, $line, $previous);
    }

    public static function logicException(string $message, int $code = 0, Throwable $previous = null): LogicException
    {
        return new LogicException($message, $code, $previous);
    }

    public static function badFunctionCallException(string $message, int $code = 0, Throwable $previous = null): BadFunctionCallException
    {
        return new BadFunctionCallException($message, $code, $previous);
    }

    public static function badMethodCallException(string $message, int $code = 0, Throwable $previous = null): BadMethodCallException
    {
        return new BadMethodCallException($message, $code, $previous);
    }

    public static function domainException(string $message, int $code = 0, Throwable $previous = null): DomainException
    {
        return new DomainException($message, $code, $previous);
    }

    public static function invalidArgumentException(string $message, int $code = 0, Throwable $previous = null): InvalidArgumentException
    {
        return new InvalidArgumentException($message, $code, $previous);
    }

    public static function lengthException(string $message, int $code = 0, Throwable $previous = null): LengthException
    {
        return new LengthException($message, $code, $previous);
    }

    public static function outOfRangeException(string $message, int $code = 0, Throwable $previous = null): OutOfRangeException
    {
        return new OutOfRangeException($message, $code, $previous);
    }

    public static function runtimeException(string $message, int $code = 0, Throwable $previous = null): RuntimeException
    {
        return new RuntimeException($message, $code, $previous);
    }

    public static function outOfBoundsException(string $message, int $code = 0, Throwable $previous = null): OutOfBoundsException
    {
        return new OutOfBoundsException($message, $code, $previous);
    }

    public static function overflowException(string $message, int $code = 0, Throwable $previous = null): OverflowException
    {
        return new OverflowException($message, $code, $previous);
    }

    public static function rangeException(string $message, int $code = 0, Throwable $previous = null): RangeException
    {
        return new RangeException($message, $code, $previous);
    }

    public static function UnderflowException(string $message, int $code = 0, Throwable $previous = null): UnderflowException
    {
        return new UnderflowException($message, $code, $previous);
    }

    public static function unexpectedValueException(string $message, int $code = 0, Throwable $previous = null): UnexpectedValueException
    {
        return new UnexpectedValueException($message, $code, $previous);
    }
}