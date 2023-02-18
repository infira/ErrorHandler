<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class RuntimeException extends \RuntimeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}