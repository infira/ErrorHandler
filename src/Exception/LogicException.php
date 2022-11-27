<?php

namespace Infira\Error\Exception;

class LogicException extends \LogicException implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}