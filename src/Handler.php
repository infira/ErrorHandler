<?php declare(strict_types=1);

namespace Infira\Error;

use Infira\Utils\Is as Is;
use PHPMailer\PHPMailer\PHPMailer as PHPMailer;
use Infira\Utils\RuntimeMemory as Rm;

/**
 * This class handles users and php errors
 */
class Handler
{
	private static $options;
	private static $trace                = null;
	public static  $debugBacktraceOption = 1; //https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	const UNDEFINED = '___undefined___';
	const BREAK     = '___break___';
	private static $isInited          = false;
	private static $defaultDateFormat = 'd.m.Y H:i:s';
	
	/**
	 * Handler constructor.
	 *
	 * @param array $options
	 *  errorLevel - -1,//-1 means all erors, see https://www.php.net/manual/en/function.error-reporting.php
	 *  dateFormat - d.m.Y H:i:s
	 *  email - use to send error to email. defaults to null, a Object which has isHTML() and Send() method in it. I recommend PHPMailer (https://github.com/PHPMailer/PHPMailer)
	 *  debugBacktraceOption - https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	 */
	public function __construct(array $options = [])
	{
		self::$isInited             = true;
		$default                    = ['dateFormat' => self::$defaultDateFormat, 'email' => null, 'errorLevel' => -1, 'debugBacktraceOption' => 0];
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
	 * @return mixed
	 */
	private static function trigger(string $message, int $code = E_USER_ERROR, string $file = '', int $line = 0)
	{
		$error = self::makeError($message, $code, $file, $line);
		if (self::getOpt('email') !== null)
		{
			self::mail($error);
		}
		throw $error;
	}
	
	private static function makeError(string $message, int $code = E_USER_ERROR, string $file = '', int $line = 0): Error
	{
		$error = new Error($message, $code, 1, $file, $line);
		$error->setTrace(debug_backtrace(self::$debugBacktraceOption));
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
	
	private static function mail(Error $Error)
	{
		$email  = self::getOpt('email');
		$Mailer = null;
		if (is_string($email))
		{
			if (Is::email($email))
			{
				$Mailer = new PHPMailer();
				$Mailer->addAddress(self::getOpt('email'));
			}
		}
		else if (is_callable($email))
		{
			$Mailer = $email();
		}
		else
		{
			$Mailer = $email;
		}
		if (is_object($Mailer))
		{
			if (!$Mailer->Subject)
			{
				$Mailer->Subject = 'Page ErrorReporting - ' . $Error->getTitle();
			}
			$Mailer->Body = $Error->getHTMLTable();
			$Mailer->isHTML(true);
			$Mailer->Send();
		}
	}
	
	//########################################################################################### SOF Public Actions
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
	public static function addExtraErrorInfo($name, $data = self::UNDEFINED)
	{
		if (is_array($name) and $data === self::UNDEFINED)
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
	 * Send error to email only, code will continue executing
	 * will work when email is configured
	 * Uses PHPMailer
	 *
	 * @param string $message
	 * @param mixed  $extra - extra data will be added to error message
	 * @return void
	 */
	public static function raiseEmail(string $message, $extra = null): void
	{
		if ($extra)
		{
			self::addExtraErrorInfo($extra);
		}
		self::mail(self::makeError($message, E_USER_ERROR));
	}
	
	/**
	 * Error exception handler, will return error as HTML
	 *
	 * @param \Throwable $throwable
	 * @return Error
	 */
	public function catch(\Throwable $throwable): Error
	{
		$trace = $throwable->getTrace();
		if (self::$debugBacktraceOption === DEBUG_BACKTRACE_IGNORE_ARGS)
		{
			foreach ($trace as $k => $arg)
			{
				if (isset($trace[$k]['args']))
				{
					unset($trace[$k]['args']);
				}
			}
		}
		
		if (!checkArray($trace))
		{
			$trace = [];
		}
		$trace   = array_reverse($trace);
		$trace[] = ['file' => $throwable->getFile(), 'line' => $throwable->getLine()];
		$error   = self::makeError($throwable->getMessage(), $throwable->getCode(), $throwable->getFile(), $throwable->getLine());
		$error->setTrace(array_reverse($trace));
		$error->stack();
		
		if (self::getOpt('email') !== null)
		{
			self::mail($error);
		}
		
		return $error;
	}
	//########################################################################################### EOF Public Actions
}

?>