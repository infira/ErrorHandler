<?php

namespace Infira\Error\Exception;

trait ThrowableDebugDataTrait
{
    private mixed $data = null;

    public function getDebugData(): mixed
    {
        return $this->data;
    }

    /**
     * Attach data to exception for later debugging
     * @param  mixed  $data
     * @return $this
     */
    public function width(mixed $data): static
    {
        $this->data = $data;

        return $this;
    }
}