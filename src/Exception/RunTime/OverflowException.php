<?php

namespace Infira\Error\Exception\RunTime;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class OverflowException extends \OverflowException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}