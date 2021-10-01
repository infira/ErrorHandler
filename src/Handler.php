<?php declare(strict_types=1);

namespace Infira\Error;

use Infira\Utils\RuntimeMemory as Rm;

/**
 * This class handles users and php errors
 */
class Handler
{
	private static $options;
	private static $trace                = null;
	public static  $debugBacktraceOption = 1; //https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	private static $isInited             = false;
	private static $defaultDateFormat    = 'd.m.Y H:i:s';
	
	/**
	 * Handler constructor.
	 *
	 * @param array $options
	 *  errorLevel - -1,//-1 means all erors, see https://www.php.net/manual/en/function.error-reporting.php
	 *  dateFormat - d.m.Y H:i:s
	 *  debugBacktraceOption - https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	 */
	public function __construct(array $options = [])
	{
		self::$isInited             = true;
		$default                    = ['dateFormat' => self::$defaultDateFormat, 'beforeTrigger' => null, 'errorLevel' => -1, 'debugBacktraceOption' => 0, 'basePath' => ''];
		self::$options              = array_merge($default, $options);
		self::$debugBacktraceOption = self::$options['debugBacktraceOption'];
		
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(self::getOpt('errorLevel'));
		
		set_error_handler(function ($errorNo, $errorMsg, $errFile, $errLine)
		{
			self::trigger($errorMsg, $errorNo, $errFile, $errLine);
		});
		register_shutdown_function(function ()
		{
			$error = error_get_last();
			if ($error !== null)
			{
				self::trigger($error['message'], $error['type']);
			}
		}, self::getOpt('errorLevel'));
	}
	
	/**
	 * trigger error
	 *
	 * @param string $message
	 * @param int    $code
	 * @param string $file
	 * @param int    $line
	 * @throws Error
	 */
	private static function trigger(string $message, int $code = E_USER_ERROR, string $file = '', int $line = 0)
	{
		$error = self::makeError($message, $code, $file, $line);
		if (is_callable($beforeTrigger = self::getOpt('beforeTrigger')))
		{
			$res = $beforeTrigger($error);
			if ($res === false)
			{
				return false;
			}
		}
		throw $error;
	}
	
	public static function makeError(string $message, int $code = E_USER_ERROR, string $file = '', int $line = 0): Error
	{
		$error = new Error($message, $code, 1, $file, $line);
		$error->setTrace(debug_backtrace(self::$debugBacktraceOption), self::$debugBacktraceOption, self::getOpt('basePath'));
		$error->setDateFormat(self::getOpt('dateFormat') ? self::getOpt('dateFormat') : self::$defaultDateFormat);
		$error->stack();
		
		return $error;
	}
	
	/**
	 * Get option value
	 *
	 * @param string $name
	 * @return mixed
	 */
	private static function getOpt(string $name)
	{
		return self::$options[$name];
	}
	
	public static function clearExtraErrorInfo()
	{
		Rm::Collection('ErrorHandlerExtraInfo')->flush();
	}
	
	/**
	 * Add extra to error output for more extended information
	 *
	 * @param string|array $name - string, or in case of array ,every key will be added as extra data key to error output
	 * @param mixed        $data [$name=>$data] will be added to error output
	 */
	public static function addExtraErrorInfo($name, $data = null)
	{
		if (is_array($name) and $data === null)
		{
			foreach ($name as $n => $v)
			{
				self::addExtraErrorInfo($n, $v);
			}
		}
		else
		{
			Rm::Collection('ErrorHandlerExtraInfo')->set($name, $data);
		}
	}
	
	/**
	 * Raise a error, code will stop executing
	 *
	 * @param string $msg
	 * @param mixed  $extra - extra data will be added to error message
	 * @throws Error
	 * @return void
	 */
	public static function raise(string $msg, $extra = null): void
	{
		if ($extra)
		{
			self::addExtraErrorInfo($extra);
		}
		self::trigger($msg, E_USER_ERROR);
	}
	
	/**
	 * Error exception catcher, will covert to Infira\Error\Error
	 *
	 * @param \Throwable $throwable
	 * @return Error
	 */
	public function catch(\Throwable $throwable): Error
	{
		$trace = $throwable->getTrace();
		if (!is_array($trace))
		{
			$trace = [];
		}
		$trace   = array_reverse($trace);
		$trace[] = ['file' => $throwable->getFile(), 'line' => $throwable->getLine()];
		$error   = self::makeError($throwable->getMessage(), $throwable->getCode(), $throwable->getFile(), $throwable->getLine());
		$error->setTrace(array_reverse($trace), self::$debugBacktraceOption, self::getOpt('basePath'));
		$error->stack();
		
		return $error;
	}
}