<?php

if (!function_exists('alert')) {
    /**
     * Triggers a E_USER_ERROR
     *
     * @param  string  $msg
     * @param  mixed  $data  - extra error info
     * @throws \Infira\Error\Exception
     */
    function alert(string $msg, mixed $data = null): void
    {
        Infira\Error\Error::trigger($msg, $data);
    }
}