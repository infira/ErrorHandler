<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class LengthException extends \LengthException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}