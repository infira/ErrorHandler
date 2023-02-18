<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class BadFunctionCallException extends \BadFunctionCallException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}