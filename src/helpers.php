<?php

use Infira\Error\Exception\TriggerException;

if (!function_exists('alert')) {
    /**
     * Triggers a E_USER_ERROR
     *
     * @param  string  $msg
     * @param  mixed  $data  - extra error info
     * @throws TriggerException
     */
    function alert(string $msg, mixed $data = null): void
    {
        Infira\Error\Error::trigger($msg, $data);
    }
}