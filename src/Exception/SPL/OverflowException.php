<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class OverflowException extends \OverflowException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}