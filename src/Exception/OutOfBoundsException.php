<?php

namespace Infira\Error\Exception;

class OutOfBoundsException extends \OutOfBoundsException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}