<?php

namespace Infira\Error\Exception;

interface ThrowableDebugDataContract
{
    public function getDebugData(): mixed;

    public function clearDebugData(): void;

    /**
     * Attach data to exception for later debugging
     *
     * @param  string|array  $name  - string, or in case of array ,every key will be added as extra data key to error output
     * @param  mixed  $data  [$name=>$data] will be added to error output
     */
    public function with(string|array $name, mixed $data = null): static;
}