<?php

namespace Infira\Error\Exception;

use Infira\Error\Capsule;

class ExceptionCapsule extends \RuntimeException
{
    public function __construct(\Throwable $caughtException, private Capsule $capsule)
    {
        parent::__construct('Captured exception by \Infira\ErrorHandler', 0, $caughtException);
    }

    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }
}