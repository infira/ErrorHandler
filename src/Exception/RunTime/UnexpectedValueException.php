<?php

namespace Infira\Error\Exception\RunTime;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class UnexpectedValueException extends \UnexpectedValueException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}