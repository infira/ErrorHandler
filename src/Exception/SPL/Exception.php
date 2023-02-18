<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class Exception extends \Exception implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}