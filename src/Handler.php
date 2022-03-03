<?php declare(strict_types=1);

namespace Infira\Error;

/**
 * This class handles users and php errors
 */
class Handler
{
	/**
	 * @param int $errorLevel
	 * @see https://www.php.net/manual/en/function.error-reporting.php
	 * @return void
	 */
	public static function register(int $errorLevel = -1)
	{
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting($errorLevel);
	}
	
	/**
	 * @param \Throwable $exception
	 * @param int        $debugBacktraceOption https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	 * @return \Infira\Error\ExceptionDataStack
	 */
	public static function compile(\Throwable $exception, int $debugBacktraceOption = DEBUG_BACKTRACE_IGNORE_ARGS): ExceptionDataStack
	{
		$basePath = dirname(debug_backtrace(0, 1)[0]['file']);
		$trace    = $exception->getTrace();
		if (!$trace) {
			$trace = debug_backtrace($debugBacktraceOption);
		}
		$trace = array_reverse($trace);
		
		return new ExceptionDataStack($exception, array_reverse($trace), $debugBacktraceOption, $basePath);
	}
}