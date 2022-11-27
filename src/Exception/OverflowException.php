<?php

namespace Infira\Error\Exception;

class OverflowException extends \OverflowException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}