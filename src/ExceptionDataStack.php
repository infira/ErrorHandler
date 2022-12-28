<?php

namespace Infira\Error;

use ErrorException;
use Infira\Error\Exception\ExceptionCapsule;
use Infira\Error\Exception\ThrowableDebugDataContract;
use Throwable;

class ExceptionDataStack
{
    public string|null $message = null;
    public string|null $time = null;
    public string|null $url = null;
    public array $exception = ['name' => null];
    public mixed $code = null;
    public mixed $severity = null;
    public array $debug = [];
    public array $trace = [];
    public string|null $requestMethod = null;
    public string|null $phpInput = null;
    public array $request = [];
    private Throwable $exceptionObject;

    public function __construct(Throwable $exception, int $traceOptions = DEBUG_BACKTRACE_IGNORE_ARGS, string $capsuleID = null)
    {
        //See https://www.php.net/manual/en/errorfunc.constants.php for descriptions
        $errorCodes = [
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

        //debug($exception);
        $realException = $exception;
        $capsuleData = null;
        $tree = &$capsuleData;
        while ($realException instanceof ExceptionCapsule) {
            $capsule = $realException->getCapsule();
            $name = $capsule->getName();
            if ($tree === null) {
                $tree[$name] = $capsule->all();
                $tree = &$tree[$name];
            }
            $realException = $realException->getPrevious();
        }
        unset($tree);
        if ($capsuleData) {
            $this->exception = array_merge($this->exception, $capsuleData);
        }
        $this->exceptionObject = $realException;

        $this->message = (isset($errorCodes[$realException->getCode()]) ? $errorCodes[$realException->getCode()].': ' : '').$realException->getMessage();
        $this->exception['name'] = '\\'.get_class($realException);
        $this->code = $realException->getCode();
        $this->severity = $realException instanceof ErrorException ? $realException->getSeverity() : null;
        $this->setTrace($realException, $traceOptions);

        if ($realException instanceof ThrowableDebugDataContract) {
            $this->exception['debugData'] = $realException->getDebugData();
        }

        if ($global = DebugCollector::all()) {
            $this->setDebug('global debugData', $global);
        }
        $this->time = date(Handler::$dateFormat).' '.Handler::$dateFormat;
        $this->phpInput = file_get_contents("php://input");
        $this->requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;

        if ($_SERVER["REQUEST_METHOD"] === 'POST' && $_POST) {
            $this->request['$_POST'] = $_POST;
        }
        if ($_GET) {
            $this->request['$_GET'] = $_GET;
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
    }

    private function setTrace(\Throwable $exception, int $traceOptions = null): void
    {
        foreach ($exception->getTrace() as $arg) {
            if (($traceOptions === DEBUG_BACKTRACE_IGNORE_ARGS) && isset($arg['args'])) {
                unset($arg['args']);
            }
            $this->trace[] = $arg;
        }
        if (
            isset($this->trace[0]['file'], $this->trace[1]['file'])
            && $this->trace[0]['file'] === $this->trace[1]['file']
        ) {
            unset($this->trace[0]);
            $this->trace = array_values($this->trace);
        }
    }

    public function setDebug(string $name, mixed $data): static
    {
        $this->debug[$name] = $data;

        return $this;
    }

    public function print(array $data = null): string
    {
        return (new Printer)->print($data ?? $this->toArray());
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static function ($v, $k) {
            if ($k === 'exceptionObject') {
                return false;
            }

            return !empty($v);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function pipe(callable $callback): ExceptionDataStack
    {
        return $callback($this);
    }

    public function getException(): \Throwable
    {
        return $this->exceptionObject;
    }
}