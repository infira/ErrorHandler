<?php

namespace Infira\Error\Exception\RunTime;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class UnderflowException extends \UnderflowException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}