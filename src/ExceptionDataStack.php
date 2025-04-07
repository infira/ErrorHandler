<?php

namespace Infira\Error;

use ErrorException;
use Infira\Error\Exception\ExceptionCapsule;
use Infira\Error\Exception\ThrowableDebugDataContract;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

class ExceptionDataStack
{
    public array $data = [];
    private Throwable $realException;
    private static ?string $errorClassFileLocation = null;

    /**
     * @param Throwable $exception
     * @param array{
     *     ignoreArgs: bool,
     *     voidInternalFiles: bool,
     *     setArgumentNames: bool,
     *     shortCallable: bool,
     * } $traceOptions
     */
    public function __construct(Throwable $exception, array $traceOptions = [])
    {
        $defaultTraceOptions = [
            'ignoreArgs' => false,
            'voidInternalFiles' => true,
            'setArgumentNames' => true,
            'shortCallable' => true,
        ];
        $traceOptions = array_merge($defaultTraceOptions, $traceOptions);
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

        $capsule = null;
        while ($exception instanceof ExceptionCapsule) {
            $capsule = $exception->getCapsule();
            $exception = $exception->getCaughtException();
        }
        $this->realException = $exception;
        unset($tree);
        $this->data = [
            'message' => (isset($errorCodes[$exception->getCode()]) ? $errorCodes[$exception->getCode()].': ' : '').$exception->getMessage(),
            'time' => date(Handler::$dateFormat),
            'exception' => [
                'class' => '\\'.get_class($exception),
                'code' => $exception->getCode()
            ],
            'global-debug' => [],
            'trace' => []
        ];
        if ($exception instanceof ErrorException) {
            $this->data['exception']['severity'] = $exception->getSeverity();
        }
        if ($capsule) {
            $this->data['exception'] = array_merge($this->data['exception'], $capsule->all());
        }
        $this->data['trace'] = $this->getTrace($exception, $traceOptions);

        if ($exception instanceof ThrowableDebugDataContract) {
            if ($exception->getDebugData()) {
                $this->data['exception']['debugData'] = $exception->getDebugData();
            }
        }

        if ($global = DebugCollector::all()) {
            $this->data['global-debug'] = $global;
        }
        $requestMethod = $_SERVER["REQUEST_METHOD"] ?? 'not-set';
        $this->data['http-request'] = [
            'url' => $_SERVER['REQUEST_URI'] ?? 'not-set',
            'method' => $requestMethod,
            'php://input' => file_get_contents("php://input")
        ];
        if ($requestMethod === 'POST' && $_POST) {
            $this->data['http-request']['$_POST'] = $_POST;
        }
        if ($_GET) {
            $this->data['http-request']['$_GET'] = $_GET;
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $url = 'http';
            if (isset($_SERVER['HTTPS'])) {
                $isHttps = strtolower($_SERVER['HTTPS']);
                if ($isHttps === 'on') {
                    $url .= 's';
                }
            }
            $this->data['http-request']['url'] = $url.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
    }

    /** @internal */
    public static function __setErrorClassFileLocation(string $path, int $line): void
    {
        self::$errorClassFileLocation = "$path:$line";
    }

    private function getTrace(Throwable $throwable, array $traceOptions): array
    {
        $trace = [];
        if ($throwable instanceof \Error) {
            $trace[] = [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine()
            ];
        }
        foreach ($throwable->getTrace() as $item) {
            $item['file'] = $item['file'] ?? '';
            $item['args'] = $item['args'] ?? [];

            $function = $item['function'] ?? '';
            $callable = $function;
            if (isset($item['class']) && isset($item['type'])) {
                $callable = $item['class'].$item['type'].$function;
            }

            if ($traceOptions['voidInternalFiles']) {
                $originalFileLine = $item['file'];
                if (isset($item['line'])) {
                    $originalFileLine .= ':'.$item['line'];
                }
                if ($callable === Error::class.'::try') {
                    continue;
                }
                if (self::$errorClassFileLocation === $originalFileLine) {
                    continue;
                }
            }

            if ($traceOptions['ignoreArgs']) {
                unset($item['args']);
            }
            else if ($traceOptions['setArgumentNames']) {
                $item['args'] = $this->setTraceArgumentNames($item);
            }

            if ($traceOptions['shortCallable']) {
                $tmpItem = $item;
                foreach (['class', 'function', 'type', 'args', 'object'] as $f) {
                    if (array_key_exists($f, $tmpItem)) {
                        unset($tmpItem[$f]);
                    }
                }

                $newItem = [
                    'file' => $item['file'],
                    'line' => $item['line'],
                ];
                if ($traceOptions['ignoreArgs']) {
                    $newItem['called'] = $callable.'()';
                }
                else if ($item['args'] ?? []) {
                    $newItem["called: $callable() with arguments"] = $item['args'];
                }
                else {
                    $newItem['called'] = $callable.'() with no arguments';
                }
                $item = array_merge(
                    $newItem,
                    $tmpItem
                );
            }
            $trace[] = $item;
        }
        return $trace;
    }

    /**
     * @param callable<array> $callback
     * @return $this
     */
    public function mapTrace(callable $callback): static
    {
        $this->data['trace'] = array_map($callback, $this->data['trace']);
        return $this;
    }

    public function print(array $data = null): string
    {
        return $this->getPrinter($data)->print();
    }

    public function getPrinter(array $data = null): Printer
    {
        return new Printer($data ?? $this->toArray());
    }

    public function toArray(): array
    {
        return array_filter($this->data, static function ($v) {
            return !empty($v);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getException(): Throwable
    {
        return $this->realException;
    }

    //region internal helpers
    private function setTraceArgumentNames(array $traceItem): array
    {
        $function = $traceItem['function'] ?? null;
        $class = $traceItem['class'] ?? null;

        if (!$class && !$function) {
            return [];
        }

        if ($class && is_string($function) && str_contains($function, '{closure}')) {
            return ['{closure}'];
        }

        if ($class && $function) {
            $ref = new ReflectionMethod($class, $function);
        }
        else {
            $ref = new ReflectionFunction($function);
        }
        $args = $traceItem['args'];
        $countPassedArguments = count($args);
        if ($countPassedArguments <= 0) {
            return [];
        }
        $names = array_map(static function (\ReflectionParameter $param) { return $param->getName(); }, $ref->getParameters());
        $countRealArguments = count($names);
        if ($countRealArguments === $countPassedArguments) {
            return array_combine($names, $args);
        }

        if ($countRealArguments > $countPassedArguments) {
            return array_combine(
                array_slice(
                    $names,
                    0,
                    $countPassedArguments
                ),
                $args
            );
        }
        //where passed arguments are more than function arguments
        return array_merge(
            array_combine(
                $names,
                array_slice(
                    $args,
                    0,
                    $countRealArguments
                )
            ),
            ['un_matched_values' => array_slice($args, $countRealArguments)]
        );
    }
    //endregion
}