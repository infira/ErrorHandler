<?php

namespace Infira\Error;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class Exception extends \Exception implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}