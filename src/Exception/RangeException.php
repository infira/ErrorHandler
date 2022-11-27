<?php

namespace Infira\Error\Exception;

class RangeException extends \RangeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}