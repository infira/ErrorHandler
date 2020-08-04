<?php declare(strict_types=1);

namespace Infira\Error;

use Infira\Utils\Http;
use Infira\Utils\Session as Sess;

/**
 * Stores error information
 *
 * @property-read string $title
 * @property-read string $msg
 * @property-read string $file
 * @property-read string $trace
 * @property-read string $time - formated date
 * @property-read string $phpInput
 * @property-read string $url
 * @property-read array  $EXTRA
 * @property-read array  $POST
 * @property-read array  $GET
 * @property-read array  $SESSION
 * @property-read array  $SESSION_ID
 * @property-read array  $SERVER
 */
class Node
{
	private $dontShowSSLVariablesOnShow;
	private $vars;
	private $trace = null;
	
	public function __construct(int $errorNo, string $errorMsg, string $errorFile, int $errorFileLine, bool $dontShowSSLVariablesOnShow, $dateFormat, $trace)
	{
		$this->dontShowSSLVariablesOnShow = $dontShowSSLVariablesOnShow;
		$this->trace                      = $trace;
		$this->vars                       = new \stdClass();
		
		//See https://www.php.net/manual/en/errorfunc.constants.php for descriptions
		$errorCodes = [0 => "Error", E_ERROR => "Fatal", E_WARNING => "Warning", E_PARSE => "Parse", E_NOTICE => "Notice", E_CORE_ERROR => "Core fatal", E_CORE_WARNING => "Core warning", E_COMPILE_ERROR => "Compile", E_COMPILE_WARNING => "Compile warning", E_USER_ERROR => "User error", E_USER_WARNING => "User warning", E_USER_NOTICE => "User notice", E_STRICT => "Strict", E_RECOVERABLE_ERROR => "Fatal", E_DEPRECATED => "Depreacated", E_USER_DEPRECATED => "User depreacated",];
		
		$this->vars->title = (isset($errorCodes[$errorNo])) ? $errorCodes[$errorNo] : "Error";
		$this->vars->msg   = $this->vars->title . ": " . $errorMsg;
		$this->vars->title .= " #" . $errorNo;
		//$this->vars->file = $errorFile . " in line " . $errorFileLine;
		$this->vars->time     = date($dateFormat);
		$this->vars->trace    = $trace;
		$this->vars->url      = Http::getCurrentUrl();
		$this->vars->phpInput = file_get_contents("php://input");
		$this->vars->EXTRA    = (isset($GLOBALS["extraErrorInfo"])) ? $GLOBALS["extraErrorInfo"] : "false";
		$this->vars->POST     = Http::getPOST();
		$this->vars->GET      = Http::getGET();
		if (Sess::$isStarted)
		{
			$this->vars->SESSION    = Sess::get();
			$this->vars->SESSION_ID = Sess::getSID();
		}
		else
		{
			$this->vars->SESSION = "SESSION WAS NOT STARTED";
		}
		$this->vars->SERVER = $_SERVER;
		if ($this->dontShowSSLVariablesOnShow === true)
		{
			foreach ($this->vars->SERVER as $var => $val)
			{
				if (substr($var, 0, 4) == "SSL_")
				{
					unset($this->vars->SERVER[$var]);
				}
			}
		}
	}
	
	public function __get($name)
	{
		return $this->vars->$name;
	}
	
	public function getVars()
	{
		return $this->vars;
	}
	
	public function toHtml()
	{
		$str = "[ERROR_MSG]<br>";
		foreach ($this->vars as $name => $val)
		{
			if ($name == "msg")
			{
				$val = '<font style="color:red">' . $val . '</font>';
			}
			elseif (!is_string($val))
			{
				$val = '<pre style="margin-top:0;display: inline">' . dump($val) . "</pre>";
			}
			$val = str_replace("[NL]", '<br>', $val);
			$str .= $name . ' : ' . $val . '<br>';
		}
		
		return $str;
	}
}

?>