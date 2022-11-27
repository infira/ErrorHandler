<?php

namespace Infira\Error\Exception;

class LengthException extends \LengthException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}