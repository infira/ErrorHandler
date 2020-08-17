<?php

use Infira\Error\Handler as Error;

function alertEmail($msg)
{
	Error::triggerEamil($msg);
}

/**
 * Triggers a E_USER_ERROR
 *
 * @param string $msg
 * @param mixed  $extra - extra error info
 * @throws \Infira\Error\InfiraError
 */
function alert(string $msg, $extra = null)
{
	Error::raise($msg, $extra);
}

function clearExtraErrorInfo()
{
	Error::clearExtraErrorInfo();
}

function addExtraErrorInfo($name, $data = Error::UNDEFINED)
{
	Error::addExtraErrorInfo($name, $data);
}