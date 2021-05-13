<?php
/**
 * Triggers a E_USER_ERROR
 *
 * @param string $msg
 * @param mixed  $extra - extra error info
 * @throws \Infira\Error\Error
 */
function alert(string $msg, $extra = null)
{
	Infira\Error\Handler::raise($msg, $extra);
}

/**
 * Clear Error handler extra info
 */
function clearExtraErrorInfo()
{
	Infira\Error\Handler::clearExtraErrorInfo();
}

/**
 * Add extra to error output for more extended information
 *
 * @param string|array $name - string, or in case of array ,every key will be added as extra data key to error output
 * @param mixed        $data [$name=>$data] will be added to error output
 */
function addExtraErrorInfo($name, $data = null)
{
	Infira\Error\Handler::addExtraErrorInfo($name, $data);
}