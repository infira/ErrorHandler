<?php

namespace Infira\Error;

use ErrorException;
use Infira\Error\Exception\ThrowableDebugDataContract;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;

class ExceptionDataStack
{
    public string|null $message = null;
    /**
     * @var class-string
     */
    public string $exception = '';
    #[ArrayShape([
        'code' => 'int',
        'file' => 'string',
        'line' => 'int',
        'severity' => 'int',
    ])] public array $error = [];
    public string|null $time = null;
    public string|null $url = null;
    public ?array $debug = [];
    public array $trace = [];
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
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        $code = $exception->getCode();
        $message = $exception->getMessage();

        $this->message = '<strong>'.($errorCodes[$code] ?? '[ERROR_MSG]').'</strong>: '.$message;
        $this->exception = '\\'.get_class($exception);
        $this->error = [
            'code' => $exception->getCode(),
            //'file' => $exception->getFile(),
            //'line' => $exception->getLine(),
            'severity' => $exception instanceof ErrorException ? $exception->getSeverity() : null
        ];
        $this->trace[] = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        $this->debug = $exception instanceof ThrowableDebugDataContract ? $exception->getDebugData() : [];
        if ($capsuleID && ($capsule = DebugCollector::getCapsuleData($capsuleID))) {
            $this->debug['capsule'] = $capsule;
        }
        if ($global = DebugCollector::all()) {
            $this->debug['global'] = $global;
        }
        $this->time = date(Handler::$dateFormat).' '.Handler::$dateFormat;

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
        foreach ($trace as $arg) {
            if (($traceOptions === DEBUG_BACKTRACE_IGNORE_ARGS) && isset($arg['args'])) {
                unset($arg['args']);
            }
            $this->trace[] = $arg;
        }
        if ((count($this->trace) > 1) && isset($this->trace[1]['file']) && $this->trace[0]['file'] === $this->trace[1]['file']) {
            unset($this->trace[0]);
            $this->trace = array_values($this->trace);
        }
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
        return get_object_vars($this);
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