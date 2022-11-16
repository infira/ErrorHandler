<?php

namespace Infira\Error\Exception;

trait ThrowableDebugDataTrait
{
    private mixed $data = null;

    public function getDebugData(): mixed
    {
        return $this->data;
    }

    public function withDebug(mixed $data): static
    {
        $this->data = $data;

        return $this;
    }
}