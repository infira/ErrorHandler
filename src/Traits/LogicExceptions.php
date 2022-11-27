<?php

namespace Infira\Error\Traits;

use Infira\Error\Exception\{
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
    //region constructors
    public static function getLogicException(string $message, int $code = 0, Throwable $previous = null): LogicException
    {
        return new LogicException($message, $code, $previous);
    }

    public static function getBadFunctionCallException(string $message, int $code = 0, Throwable $previous = null): BadFunctionCallException
    {
        return new BadFunctionCallException($message, $code, $previous);
    }

    public static function getBadMethodCallException(string $message, int $code = 0, Throwable $previous = null): BadMethodCallException
    {
        return new BadMethodCallException($message, $code, $previous);
    }

    public static function getDomainException(string $message, int $code = 0, Throwable $previous = null): DomainException
    {
        return new DomainException($message, $code, $previous);
    }

    public static function getInvalidArgumentException(string $message, int $code = 0, Throwable $previous = null): InvalidArgumentException
    {
        return new InvalidArgumentException($message, $code, $previous);
    }

    public static function getLengthException(string $message, int $code = 0, Throwable $previous = null): LengthException
    {
        return new LengthException($message, $code, $previous);
    }

    public static function getOutOfRangeException(string $message, int $code = 0, Throwable $previous = null): OutOfRangeException
    {
        return new OutOfRangeException($message, $code, $previous);
    }
    //endregion

    //region throwers
    public static function throwLogicException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getLogicException($message, $code, $previous);
    }

    public static function throwBadFunctionCallException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getBadFunctionCallException($message, $code, $previous);
    }

    public static function throwBadMethodCallException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getBadMethodCallException($message, $code, $previous);
    }

    public static function throwDomainException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getDomainException($message, $code, $previous);
    }

    public static function throwInvalidArgumentException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getInvalidArgumentException($message, $code, $previous);
    }

    public static function throwLengthException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getLengthException($message, $code, $previous);
    }

    public static function throwOutOfRangeException(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw self::getOutOfRangeException($message, $code, $previous);
    }
    //endregion
}