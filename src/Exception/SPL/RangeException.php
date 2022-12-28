<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class RangeException extends \RangeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}