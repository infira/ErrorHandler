<?php

namespace Infira\Error\Exception\SPL;

use Infira\Error\Exception\ThrowableDebugDataContract;
use Infira\Error\Exception\ThrowableDebugDataTrait;

class DomainException extends \DomainException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}