<?php

namespace Infira\Error;

use Infira\Utils\RuntimeMemory as Rm;
use Infira\Utils\Http;
use Infira\Utils\Session as Sess;
use stdClass;

class Error extends \ErrorException
{
	private $hideSSLVariables = true;
	private $stack;
	private $dateFormat       = 'd.m.Y';
	
	/*
	public function __construct($message = "", $code = 0, $severity = 1, $filename = __FILE__, $line = __LINE__, $previous = null)
	{
		$this->stack = new stdClass();
		parent::__construct($message, $code, $severity, $filename, $line, $previous);
	}
	*/
	private function makeStack()
	{
		if (!$this->stack)
		{
			$this->stack            = new stdClass();
			$this->stack->title     = '';
			$this->stack->msg       = null;
			$this->stack->time      = null;
			$this->stack->url       = null;
			$this->stack->trace     = null;
			$this->stack->extra     = null;
			$this->stack->phpInput  = null;
			$this->stack->post      = null;
			$this->stack->get       = null;
			$this->stack->session   = null;
			$this->stack->sessionID = null;
			$this->stack->server    = null;
		}
	}
	
	public function stack()
	{
		$this->makeStack();
		//See https://www.php.net/manual/en/errorfunc.constants.php for descriptions
		$errorCodes = [0 => "Error", E_ERROR => "Fatal", E_WARNING => "Warning", E_PARSE => "Parse", E_NOTICE => "Notice", E_CORE_ERROR => "Core fatal", E_CORE_WARNING => "Core warning", E_COMPILE_ERROR => "Compile", E_COMPILE_WARNING => "Compile warning", E_USER_ERROR => "User error", E_USER_WARNING => "User warning", E_USER_NOTICE => "User notice", E_STRICT => "Strict", E_RECOVERABLE_ERROR => "Fatal", E_DEPRECATED => "Depreacated", E_USER_DEPRECATED => "User depreacated",];
		
		$code    = $this->getCode();
		$message = $this->getMessage();
		
		$this->stack->title = (isset($errorCodes[$code]) ? $errorCodes[$code] : 'Error') . " #$code";
		$this->stack->msg   = $message;
		$this->stack->time  = date($this->dateFormat);
		
		$items             = [];
		$items['extra']    = Rm::Collection("ErrorHandlerExtraInfo")->getItems();
		$items['phpInput'] = file_get_contents("php://input");
		$items['POST']     = Http::getPOST();
		$items['GET']      = Http::getGET();
		if (Sess::$isStarted)
		{
			$items['SESSION']    = Sess::get();
			$items['SESSION_ID'] = Sess::getSID();
		}
		if (isset($_SERVER['HTTP_HOST']))
		{
			$items['url'] = Http::getCurrentUrl();
		}
		
		foreach ($items as $n => $v)
		{
			if ($v)
			{
				$this->stack->$n = $v;
			}
		}
		
		$this->stack->server = $_SERVER;
		//hide SSL variables
		if ($this->hideSSLVariables === true)
		{
			foreach ($this->stack->server as $var => $val)
			{
				if (substr($var, 0, 4) == "SSL_")
				{
					unset($this->stack->server[$var]);
				}
			}
		}
	}
	
	public function getTitle(): string
	{
		return $this->stack->title;
	}
	
	public function setHideSSLVariables(bool $bool)
	{
		$this->hideSSLVariables = $bool;
	}
	
	/**
	 * @param string $dateFormat
	 * @see https://www.php.net/manual/en/datetime.format.php
	 */
	public function setDateFormat(string $dateFormat): void
	{
		$this->dateFormat = $dateFormat;
	}
	
	public function setTrace(array $trace, $traceOptions = null)
	{
		$this->makeStack();
		if ($traceOptions === DEBUG_BACKTRACE_IGNORE_ARGS)
		{
			foreach ($trace as $k => $arg)
			{
				if (isset($trace[$k]['args']))
				{
					unset($trace[$k]['args']);
				}
			}
		}
		foreach ($trace as $k => $arg)
		{
			foreach (['ErrorHandler/src/Handler.php', 'ErrorHandler/src/generalMethods.php'] as $c)
			{
				if (isset($arg['file']) and substr(strtolower($arg['file']), -strlen($c)) == strtolower($c))
				{
					unset($trace[$k]);
				}
			}
		}
		$trace              = array_values($trace);
		$this->stack->trace = $trace;
	}
	
	public function getStackTrace(): array
	{
		return $this->stack->trace ? $this->stack->trace : [];
	}
	
	/**
	 * @return string
	 */
	public function getHTMLTable(): string
	{
		if (!$this->stack)
		{
			return $this->getMessage();
		}
		$str = "
		<table cellpadding='0' cellspacing='0' border='0'>
		";
		foreach ($this->stack as $name => $val)
		{
			if ($val === null)
			{
				$val = 'null';
			}
			if ($name == "msg")
			{
				$val = '<font style="color:red">' . $val . '</font>';
			}
			elseif (!is_string($val))
			{
				$val = '<pre style="margin-top:0;display: inline">' . dump($val) . "</pre>";
			}
			if ($name == 'title')
			{
				$name = '[ERROR_MSG]';
			}
			
			$str .= "<tr>
			<th style='text-align: left;vertical-align: top'>$name:&nbsp;</th>
			<td>&nbsp;$val</td>
			</tr>";
		}
		$str .= '</table>';
		
		return $str;
	}
	
	public function getStack(): stdClass
	{
		$this->makeStack();
		
		return $this->stack;
	}
}

?>