<?php

namespace Infira\Error\Exception\RunTime;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class RangeException extends \RangeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}