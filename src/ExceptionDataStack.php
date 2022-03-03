<?php

namespace Infira\Error;

class ExceptionDataStack
{
	public string|null $title         = null;
	public string|null $exception     = null;
	public string|null $msg           = null;
	public string|null $time          = null;
	public string|null $url           = null;
	public array       $trace         = [];
	public array       $debug         = [];
	public array       $gloalDebug    = [];
	public string|null $requestMethod = null;
	public string|null $phpInput      = null;
	public array       $post          = [];
	public array       $get           = [];
	public array       $session       = [];
	public string|null $sessionID     = null;
	public array       $server        = [];
	
	public function __construct(\Throwable $exception, array $trace, $traceOptions = null)
	{
		//See https://www.php.net/manual/en/errorfunc.constants.php for descriptions
		$errorCodes = [
			0                   => "ErrorException",
			E_ERROR             => "Fatal",
			E_WARNING           => "Warning",
			E_PARSE             => "Parse",
			E_NOTICE            => "Notice",
			E_CORE_ERROR        => "Core fatal",
			E_CORE_WARNING      => "Core warning",
			E_COMPILE_ERROR     => "Compile",
			E_COMPILE_WARNING   => "Compile warning",
			E_USER_ERROR        => "User error",
			E_USER_WARNING      => "User warning",
			E_USER_NOTICE       => "User notice",
			E_STRICT            => "Strict",
			E_RECOVERABLE_ERROR => "Fatal",
			E_DEPRECATED        => "Deprecated",
			E_USER_DEPRECATED   => "User deprecated",
		];
		
		$code    = $exception->getCode();
		$message = $exception->getMessage();
		
		$this->title     = $errorCodes[$code] ?? '[ERROR_MSG]';
		$this->exception = get_class($exception);
		$this->msg       = $message;
		$this->time      = date('d.m.Y H:i:s') . ' [d.m.Y H:i:s]';
		
		$debug = new \stdClass();
		if ($exception instanceof \Error or $exception instanceof \Exception) {
			$debug->code = $exception->getCode();
			$debug->file = $exception->getFile();
			$debug->line = $exception->getLine();
		}
		if ($exception instanceof \ErrorException) {
			$debug->severity = $exception->getSeverity();
		}
		if ($exception instanceof AlertException) {
			$debug->data = $exception->getData();
		}
		
		$this->debug         = (array)$debug;
		$this->gloalDebug    = GlobalErrorData::all();
		$this->phpInput      = file_get_contents("php://input");
		$this->requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
		$this->post          = $this->requestMethod == 'POST' ? $_POST : [];
		$this->get           = $_GET;
		if (session_status() === PHP_SESSION_ACTIVE) {
			$this->session   = $_SESSION;
			$this->sessionID = session_id();
		}
		if (isset($_SERVER['HTTP_HOST'])) {
			$url = 'http';
			if (isset($_SERVER['HTTPS'])) {
				$isHttps = strtolower($_SERVER['HTTPS']);
				if ($isHttps == 'on') {
					$url .= 's';
				}
			}
			$this->url = $url . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		
		$this->server = $_SERVER;
		//hide SSL variables
		foreach ($this->server as $var => $val) {
			if (str_starts_with($var, "SSL_")) {
				unset($this->server[$var]);
			}
		}
		
		$this->setTrace($trace, $traceOptions);
	}
	
	private function setTrace(array $trace, int $traceOptions = null)
	{
		$baseBath = Handler::$basePath ?: getcwd();
		foreach ($trace as $k => $arg) {
			if ($traceOptions === DEBUG_BACKTRACE_IGNORE_ARGS) {
				if (isset($arg['args'])) {
					unset($trace[$k]['args']);
				}
			}
			if (isset($arg['file']) and $baseBath) {
				$trace[$k]['file'] = str_replace($baseBath, '', $arg['file']);
			}
		}
		$trace       = array_values($trace);
		$this->trace = $trace;
	}
	
	public function getHTMLTable(): string
	{
		$str = "
		<table cellpadding='0' cellspacing='0' border='0'>
		";
		foreach ($this->toArray() as $name => $val) {
			if ($val === null) {
				$val = 'null';
			}
			if ($name == "msg") {
				$val = '<font style="color:red">' . $val . '</font>';
			}
			elseif (!is_string($val)) {
				if (is_array($val) or is_object($val)) {
					$dump = print_r($val, true);
				}
				else {
					ob_start();
					var_dump($val);
					$dump = ob_get_clean();
				}
				$val = '<pre style="margin-top:0;display: inline">' . $dump . "</pre>";
			}
			if ($name == 'title') {
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
	
	public function toArray(): array
	{
		$data = [];
		foreach (get_object_vars($this) as $key => $val) {
			if (empty($val)) {
				continue;
			}
			$data[$key] = $val;
		}
		
		return $data;
	}
}