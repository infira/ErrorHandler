<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class UnexpectedValueException extends \UnexpectedValueException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}