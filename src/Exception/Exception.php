<?php

namespace Infira\Error\Exception;

class Exception extends \Exception implements ThrowableDebugDataContract
{
    use ThrowableDebugDataTrait;
}