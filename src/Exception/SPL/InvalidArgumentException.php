<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class InvalidArgumentException extends \InvalidArgumentException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}