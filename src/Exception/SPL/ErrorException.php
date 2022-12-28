<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class ErrorException extends \ErrorException implements ThrowableDebugDataContract //
{
    use ThrowableDebugDataTrait;
}