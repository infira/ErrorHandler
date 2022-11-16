<?php

namespace Infira\Error;

use ErrorException;
use Infira\Error\Exception\Exception;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;

class ExceptionDataStack
{
    public string|null $name = null;
    public string|null $message = null;
    #[ArrayShape([
        'class' => 'string', //class-string
        'code' => 'int',
        'file' => 'string',
        'line' => 'int',
        'debug' => 'array|null',
        'capsule' => 'array|null',
    ])] public array $exception = [];
    public string|null $time = null;
    public string|null $url = null;

    public array $trace = [];
    public array $globalDebug = [];
    public string|null $requestMethod = null;

    /**
     * file_get_contents("php://input")
     * @var string|null
     */
    public string|null $phpInput = null;
    /**
     * Data from $_POST
     * @var array
     */
    public array $post = [];
    /**
     * Data from $_GET
     * @var array
     */
    public array $get = [];
    /**
     * Data from $_SESSION
     * @var array
     */
    public array $session = [];
    public string|null $sessionID = null;

    /**
     * Data from $_SERVER
     * @var array
     */
    public array $server = [];

    /**
     * @param  Throwable  $exception
     * @param  array  $trace
     * @param $traceOptions
     * @param  string|null  $capsuleID
     */
    public function __construct(Throwable $exception, array $trace, $traceOptions = null, string $capsuleID = null)
    {
        //See https://www.php.net/manual/en/errorfunc.constants.php for descriptions
        $errorCodes = [
            0 => "ErrorException",
            E_ERROR => "Fatal",
            E_WARNING => "Warning",
            E_PARSE => "Parse",
            E_NOTICE => "Notice",
            E_CORE_ERROR => "Core fatal",
            E_CORE_WARNING => "Core warning",
            E_COMPILE_ERROR => "Compile",
            E_COMPILE_WARNING => "Compile warning",
            E_USER_ERROR => "User error",
            E_USER_WARNING => "User warning",
            E_USER_NOTICE => "User notice",
            E_STRICT => "Strict",
            E_RECOVERABLE_ERROR => "Fatal",
            E_DEPRECATED => "Deprecated",
            E_USER_DEPRECATED => "User deprecated",
        ];

        $code = $exception->getCode();
        $message = $exception->getMessage();

        $this->name = $errorCodes[$code] ?? '[ERROR_MSG]';
        $this->message = $message;
        $this->exception = [
            'class' => '\\'.get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'debug' => $exception instanceof Exception ? $exception->getDebugData() : null,
            'capsule' => $capsuleID ? DebugCollector::all($capsuleID) : null,
        ];
        $this->time = date(Handler::$dateFormat).' '.Handler::$dateFormat;

        if ($exception instanceof ErrorException) {
            $this->exception['severity'] = $exception->getSeverity();
        }
        $this->globalDebug = DebugCollector::all();
        $this->phpInput = file_get_contents("php://input");
        $this->requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
        $this->post = $this->requestMethod === 'POST' ? $_POST : [];
        $this->get = $_GET;
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->session = $_SESSION;
            $this->sessionID = session_id();
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $url = 'http';
            if (isset($_SERVER['HTTPS'])) {
                $isHttps = strtolower($_SERVER['HTTPS']);
                if ($isHttps === 'on') {
                    $url .= 's';
                }
            }
            $this->url = $url.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
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

    private function setTrace(array $trace, int $traceOptions = null): void
    {
        $baseBath = Handler::$basePath ?: getcwd();
        foreach ($trace as $k => $arg) {
            if (($traceOptions === DEBUG_BACKTRACE_IGNORE_ARGS) && isset($arg['args'])) {
                unset($arg['args']);
            }
            if (isset($arg['file']) && $baseBath) {
                $arg['file'] = str_replace($baseBath, '', $arg['file']);
            }
            $trace[$k] = $arg;
        }
        $trace = array_values($trace);
        $this->trace = $trace;
    }

    public function toHTMLTable(array $data = null): string
    {
        $data = $data ?: $this->toArray();
        $str = "
		<table>
		";
        foreach ($data as $name => $val) {
            if ($val === null) {
                $val = 'null';
            }
            if ($name === 'msg') {
                $val = '<span style="color:red">'.$val.'</span>';
            }
            elseif (!is_string($val)) {
                if (is_array($val) || is_object($val)) {
                    $dump = print_r($val, true);
                }
                else {
                    ob_start();
                    var_dump($val);
                    $dump = ob_get_clean();
                }
                $val = '<pre style="margin-top:0;display: inline">'.$dump."</pre>";
            }
            if ($name === 'title') {
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

    public function map(callable $callback): ExceptionDataStack
    {
        return $callback($this);
    }
}