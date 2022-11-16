<?php

namespace Infira\Error\Exception\RunTime;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class OutOfBoundsException extends \OutOfBoundsException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}