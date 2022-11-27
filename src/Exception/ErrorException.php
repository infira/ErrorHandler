<?php

namespace Infira\Error\Exception;

class ErrorException extends \ErrorException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}