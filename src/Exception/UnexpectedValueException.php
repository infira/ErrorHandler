<?php

namespace Infira\Error\Exception;

class UnexpectedValueException extends \UnexpectedValueException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}