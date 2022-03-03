<?php
if (!function_exists('alert')) {
	/**
	 * Triggers a E_USER_ERROR
	 *
	 * @param string $msg
	 * @param mixed  $data - extra error info
	 * @throws \Infira\Error\AlertException
	 */
	function alert(string $msg, mixed $data = null)
	{
		Infira\Error\Error::trigger($msg, $data);
	}
}