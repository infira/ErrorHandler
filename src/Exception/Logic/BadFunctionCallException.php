<?php

namespace Infira\Error\Exception\Logic;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class BadFunctionCallException extends \BadFunctionCallException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}