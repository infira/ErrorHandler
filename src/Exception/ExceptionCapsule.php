<?php

namespace Infira\Error\Exception;

use Infira\Error\Capsule;
use Throwable;

class ExceptionCapsule extends \RuntimeException
{
    private Throwable $caughtException;

    public function __construct(Throwable $caughtException, private Capsule $capsule)
    {
        $this->caughtException = $caughtException;
        parent::__construct('Captured exception by \Infira\ErrorHandler');
    }

    public function getCapsule(): Capsule
    {
        return $this->capsule;
    }

    /**
     * @return Throwable
     */
    public function getCaughtException(): Throwable
    {
        return $this->caughtException;
    }
}