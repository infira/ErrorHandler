<?php

namespace Infira\Error\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}