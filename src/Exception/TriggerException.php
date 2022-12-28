<?php

namespace Infira\Error\Exception;

class TriggerException extends SPL\Exception
{
    use ThrowableDebugDataTrait;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}