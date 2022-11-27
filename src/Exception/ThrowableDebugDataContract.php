<?php

namespace Infira\Error\Exception;

interface ThrowableDebugDataContract
{
    public function getDebugData(): mixed;

    public function width(mixed $data): static;
}