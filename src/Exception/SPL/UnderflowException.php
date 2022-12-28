<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class UnderflowException extends \UnderflowException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}