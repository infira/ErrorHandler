<?php

namespace Infira\Error;

class Printer
{
    public function print(array $data): string
    {
        $str = '
        <pre style="tab-size: 12px;">
		<table>
		';
        foreach ($data as $name => $val) {
            if ($this->canCastToString($val)) {
                $val = (string)$val;
            }
            elseif ($name === 'trace') {
                $val = $this->pre($this->printTrace($val));
            }
            else {
                $val = $this->pre($val);
            }

            $str .= "<tr>
			<th style='text-align: left;vertical-align: top;padding: 2px'>$name</th>
			<td style='padding: 2px'>$val</td>
			</tr>";
        }
        $str .= '</table></pre>';

        return $str;
    }

    private function printTrace(array $trace): array
    {
        return array_map(function ($trace) {
            if (!array_key_exists('args', $trace)) {
                return $trace;
            }

            return $this->setTraceArgumentNames($trace);
        }, $trace);
    }

    private function setTraceArgumentNames(array $trace): array
    {
        if (!$trace['args']) {
            return $trace;
        }
        //debug($trace['args'][0] ?? []);
        $function = $trace['function'] ?? null;
        $class = $trace['class'] ?? null;

        if ($class && is_string($function) && str_contains($function, '{closure}')) {
            return $trace;
        }

        if (is_string($function) && !function_exists($function)) {
            //if (in_array($function, ['require', 'require_once'])) {
            //  debug(function_exists($function));
            //exit;

            return $trace;
        }

        if ($class && $function) {
            $ref = new \ReflectionMethod($class, $function);
        }
        else {
            $ref = new \ReflectionFunction($function);
        }
        $args = $trace['args'];
        $countValues = count($args);
        $names = array_map(static function (\ReflectionParameter $param) {
            return $param->getName();
        }, $ref->getParameters());
        $countNames = count($names);;
        if ($countNames === $countValues) {
            $args = array_combine($names, $args);
        }
        elseif ($countNames > $countValues) {
            $args = array_merge(
                array_combine(array_slice($names, 0, $countValues), $args),
                ['un_matched_parameters' => array_slice($names, $countValues)]
            );
        }
        else {
            $args = array_merge(
                array_combine($names, array_slice($args, 0, $countNames)),
                ['un_matched_values' => array_slice($args, $countNames)]
            );
        }

        if ($class) {
            $calledAt = $class.$trace['type'].$function;
            unset($trace['class'], $trace['type'], $trace['function'], $trace['args']);
            $trace['calledAt: '.$calledAt] = $args;

            return $trace;
        }
        $trace['args'] = $args;

        return $trace;
    }

    private function canCastToString(mixed $value): bool
    {
        return is_scalar($value)
            || is_null($value)
            || (is_object($value) && method_exists($value, '__toString'));
    }

    private function pre($var): string
    {
        return '<pre>'.$this->dump($var).'</pre>';
    }

    private function dump(mixed $var): string
    {
        if (is_array($var) || is_object($var)) {
            return print_r($var, true);
        }

        return $this->varDump($var);
    }

    private function varDump(mixed $var): string
    {
        ob_start();
        var_dump($var);

        return ob_get_clean();
    }

}