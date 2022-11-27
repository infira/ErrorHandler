<?php

namespace Infira\Error\Exception;

class RuntimeException extends \RuntimeException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}