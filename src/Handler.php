<?php declare(strict_types=1);

namespace Infira\Error;

use Infira\Utils\Is as Is;
use Infira\Error\Node as ErrorNode;
use PHPMailer\PHPMailer\PHPMailer as PHPMailer;
use Infira\Utils\RuntimeMemory as Rm;

/**
 * This class handles users and php errors
 */
class Handler
{
	private static $dontShowSSLVariablesOnShow = true;
	private static $options;
	private static $trace                      = null;
	public static  $debugBacktraceOption       = 1; //https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	const UNDEFINED = '___undefined___';
	const BREAK     = '___break___';
	
	/**
	 * Handler constructor.
	 *
	 * @param array $options
	 *  errorLevel - -1,//-1 means all erors, see https://www.php.net/manual/en/function.error-reporting.php
	 *  dateFormat - d.m.Y H:i:s
	 *  mailer - use to send error to email. defaults to null, a Object which has isHTML() and Send() method in it. I recommend PHPMailer (https://github.com/PHPMailer/PHPMailer)
	 *  debugBacktraceOption - https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	 *  beforeThrow - optional callable passed ErrorNode, will be called just before throw, if Handler::BREAK is returned, then throw new will not trigger. can be ise for logging
	 */
	public function __construct(array $options = [])
	{
		$default                    = ['dateFormat' => 'd.m.Y H:i:s', 'email' => null, 'beforeThrow' => null, 'errorLevel' => -1, 'debugBacktraceOption' => 0];
		self::$options              = array_merge($default, $options);
		self::$debugBacktraceOption = self::$options['debugBacktraceOption'];
		
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(self::getOpt('errorLevel'));
		
		set_error_handler(function ($errorNo, $errorMsg, $errFile, $errLine)
		{
			self::setTrace(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
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
	 * @param string $errorMsg
	 * @param int    $errorNo
	 * @param string $errorFile
	 * @param int    $errorLine
	 * @throws InfiraError
	 * @return mixed
	 */
	private static function trigger(string $errorMsg, int $errorNo = E_USER_ERROR, string $errorFile = '', int $errorLine = 0)
	{
		$ErrorNode = self::constructErrorNode($errorMsg, $errorNo, $errorFile, $errorLine);
		if (self::getOpt('email') !== null)
		{
			self::mail($ErrorNode);
		}
		if (self::getOpt('beforeThrow') !== null)
		{
			if (self::beforeThrow($ErrorNode) !== self::BREAK)
			{
				throw new InfiraError($ErrorNode->toHtml());
			}
		}
		else
		{
			throw new InfiraError($ErrorNode->toHtml());
		}
	}
	
	private static function constructErrorNode(string $errorMsg, int $errorNo = E_USER_ERROR, string $errorFile = '', int $errorLine = 0)
	{
		$ErrorNode   = new Node($errorNo, $errorMsg, $errorFile, $errorLine, self::$dontShowSSLVariablesOnShow, self::getOpt('dateFormat'), self::$trace);
		self::$trace = null;
		
		return $ErrorNode;
	}
	
	/**
	 * set trace to upcoming error
	 *
	 * @param array $trace
	 */
	private static function setTrace($trace)
	{
		self::$trace = $trace;
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
	
	//########################################################################################### SOF Actions
	
	private static function mail(Node $ErrorNode)
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
		else
		{
			$Mailer = $email;
		}
		if (is_object($Mailer))
		{
			if (!$Mailer->Subject)
			{
				$Mailer->Subject = 'Page ErrorReporting - ' . $ErrorNode->title;
			}
			$Mailer->Body = $ErrorNode->toHtml();
			$Mailer->isHTML(true);
			$Mailer->Send();
		}
	}
	
	private static function beforeThrow(Node $ErrorNode)
	{
		$callable = self::getOpt('beforeThrow');
		if (!is_callable($callable))
		{
			return false;
		}
		$callable = self::getOpt('beforeThrow');
		$callable($ErrorNode);
	}
	//########################################################################################### EOF Actions
	
	//########################################################################################### SOF Public Actions
	public static function clearExtraErrorInfo()
	{
		Rm::Collection('ErrorHandlerExtraInfo')->flush();
	}
	
	/**
	 * Add extra to error output for more extended information
	 *
	 * @param string $name
	 * @param mixed  $data - will add to error output
	 */
	public static function addExtraErrorInfo(string $name, $data = self::UNDEFINED)
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
	 * @throws InfiraError
	 * @return void
	 */
	public static function raise($msg, $extra = null): void
	{
		if ($extra)
		{
			self::addExtraErrorInfo($extra);
		}
		self::setTrace(debug_backtrace(self::$debugBacktraceOption));
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
		self::mail(self::constructErrorNode($message));
	}
	
	/**
	 * Error exception handler, will return error as HTML
	 *
	 * @param \Throwable $Exception
	 * @return string
	 */
	public function catch(\Throwable $Exception): string
	{
		$trace = $Exception->getTrace();
		if (!checkArray($trace))
		{
			$trace = [];
		}
		$trace   = array_reverse($trace);
		$trace[] = ['file' => $Exception->getFile(), 'line' => $Exception->getLine()];
		self::setTrace(array_reverse($trace));
		$ErrorNode = self::constructErrorNode($Exception->getMessage(), $Exception->getCode(), $Exception->getFile(), $Exception->getLine());
		
		return $ErrorNode->toHtml();
	}
	//########################################################################################### EOF Public Actions
}

?>