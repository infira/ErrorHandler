<?php

namespace Infira\Error\Exception;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class BadMethodCallException extends \BadMethodCallException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}