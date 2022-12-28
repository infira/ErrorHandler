<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class LogicException extends \LogicException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}