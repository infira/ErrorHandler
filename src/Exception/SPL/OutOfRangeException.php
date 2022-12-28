<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class OutOfRangeException extends \OutOfRangeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}