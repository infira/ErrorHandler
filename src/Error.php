<?php declare(strict_types=1);

namespace Infira\Error;

class Error
{
	/**
	 * Raise a error, code will stop executing
	 *
	 * @param string $msg
	 * @param mixed  $data - extra data will be added to error message
	 * @throws AlertException
	 * @return void
	 */
	public static function trigger(string $msg, mixed $data = null): void
	{
		throw new AlertException($msg, $data);
	}
	
	public static function clearDebug()
	{
		GlobalErrorData::flush();
	}
	
	/**
	 * Add extra to error output for more extended information
	 *
	 * @param string|array $name - string, or in case of array ,every key will be added as extra data key to error output
	 * @param mixed        $data [$name=>$data] will be added to error output
	 */
	public static function addDebug(string|array $name, mixed $data = null)
	{
		if (is_array($name) and $data === null) {
			foreach ($name as $n => $v) {
				self::addDebug($n, $v);
			}
		}
		else {
			GlobalErrorData::set($name, $data);
		}
	}
}