<?php declare(strict_types=1);

namespace Infira\Error;

use Infira\Utils\Is as Is;
use Infira\Error\Node as ErrorNode;
use PHPMailer\PHPMailer\PHPMailer as PHPMailer;

/**
 * This class handles users and php errors
 */
class Handler
{
	private static $dontShowSSLVariablesOnShow = true;
	private static $options;
	private static $trace                      = null;
	public static  $debugBacktraceOption       = 1; //https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	
	
	/**
	 * Handler constructor.
	 *
	 * @param array $options
	 *  env - dev,stable (stable env does not display full errors erros
	 *  stableDefaultMsg - when env is stable this error message is displayed
	 *  dateFormat - defaults to null, string for email or you can use your own PHPMailer object
	 *  email - defaults to null, string for email or you can use your own PHPMailer object
	 *  debugBacktraceOption - https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
	 *  log - callable passed ErrorNode
	 */
	public function __construct(array $options = [])
	{
		$default                    = ["env" => "stable", "stableDefaultMsg" => "oops...something went wrong", "dateFormat" => "d.m.Y H:i:s", "email" => null, "log" => null, "errorLevel" => -1, "debugBacktraceOption" => 0];
		self::$options              = array_merge($default, $options);
		self::$debugBacktraceOption = self::$options["debugBacktraceOption"];
		if (!in_array(self::getOpt("env"), ["stable", "dev"]))
		{
			exit("unknown envinronment " . self::getOpt("env"));
		}
		
		ini_set('display_errors', "1");
		ini_set('display_startup_errors', "1");
		error_reporting(self::getOpt("errorLevel"));
		
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
				self::trigger($error['message'], $error["type"]);
			}
		}, self::getOpt("errorLevel"));
	}
	
	/**
	 * Send error to email only, code will continue executeing
	 *
	 * @param $message
	 */
	public static function triggerEamil(string $message)
	{
		self::mail(self::constructErrorNode($message));
	}
	
	/**
	 * trigger error
	 *
	 * @param string $errorMsg
	 * @param int    $errorNo
	 * @param string $errorFile
	 * @param int    $errorLine
	 * @return mixed
	 */
	public static function trigger(string $errorMsg, int $errorNo = E_USER_ERROR, string $errorFile = "", int $errorLine = 0)
	{
		$ErrorNode = self::constructErrorNode($errorMsg, $errorNo, $errorFile, $errorLine);
		if (self::getOpt("email") !== null)
		{
			self::mail($ErrorNode);
		}
		if (self::getOpt("log") !== null)
		{
			self::log($ErrorNode);
		}
		if (self::getOpt("env") == "stable")
		{
			return self::getOpt("stableDefaultMsg");
		}
		
		throw new \Infira\Error\Error($ErrorNode->toHtml());
	}
	
	private static function constructErrorNode(string $errorMsg, int $errorNo = E_USER_ERROR, string $errorFile = "", int $errorLine = 0)
	{
		$ErrorNode   = new Node($errorNo, $errorMsg, $errorFile, $errorLine, self::$dontShowSSLVariablesOnShow, self::getOpt("dateFormat"), self::$trace);
		self::$trace = null;
		
		return $ErrorNode;
	}
	
	/**
	 * set trace to upcoming error
	 *
	 * @param array $trace
	 */
	public static function setTrace($trace)
	{
		self::$trace = $trace;
	}
	
	/**
	 * Error exception handler
	 *
	 * @param \Throwable $Exception
	 * @return string
	 */
	public function catch(\Throwable $Exception)
	{
		$trace = $Exception->getTrace();
		if (!checkArray($trace))
		{
			$trace = [];
		}
		$trace   = array_reverse($trace);
		$trace[] = ["file" => $Exception->getFile(), "line" => $Exception->getLine()];
		self::setTrace(array_reverse($trace));
		$ErrorNode = self::constructErrorNode($Exception->getMessage(), $Exception->getCode(), $Exception->getFile(), $Exception->getLine());
		
		return $ErrorNode->toHtml();
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
		$email  = self::getOpt("email");
		$Mailer = null;
		if (is_string($email))
		{
			if (Is::email($email))
			{
				$Mailer = new PHPMailer();
				$Mailer->addAddress(self::getOpt("email"));
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
				$Mailer->Subject = "Page ErrorReporting - " . $ErrorNode->title;
			}
			$Mailer->Body = $ErrorNode->toHtml();
			$Mailer->isHTML(true);
			$Mailer->Send();
		}
	}
	
	private static function log(Node $ErrorNode)
	{
		if (!is_callable(self::getOpt("log")))
		{
			return false;
		}
		$callable = self::getOpt("log");
		$callable($ErrorNode);
	}
	//########################################################################################### EOF Actions
}

?>