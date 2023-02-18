<?php

namespace Infira\Error\Exception;

interface ThrowableDebugDataContract
{
    public function getDebugData(): mixed;

    /**
     * Attach data to exception for later debugging
     *
     * @param  string|array  $name  - string, or in case of array ,every key will be added as extra data key to error output
     * @param  mixed  $data  [$name=>$data] will be added to error output
     */
    public function width(string|array $name, mixed $data = null): static;
}