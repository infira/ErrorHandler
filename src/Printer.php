<?php

namespace Infira\Error;

class Printer
{
    public function __construct(private array $data = []) {}

    public function print(): string
    {
        $str = '
        <pre style="tab-size: 12px;">
		<table>
		';
        foreach ($this->data as $name => $val) {
            if ($this->canCastToString($val)) {
                $val = (string)$val;
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

    /**
     * @param callable<array> $callback
     * @return $this
     */
    public function mapTrace(callable $callback): static
    {
        $this->data['trace'] = array_map($callback, $this->data['trace']);
        return $this;
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