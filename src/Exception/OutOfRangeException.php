<?php

namespace Infira\Error\Exception;

class OutOfRangeException extends \OutOfRangeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}