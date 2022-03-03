<?php

namespace Infira\Error;

class ExceptionHandler
{
	private ExceptionDataStack $stack;
	
	public function __construct(\Throwable $exception)
	{
		$this->stack = new ExceptionDataStack($exception);
	}
	
	public function setTrace(array $trace, $traceOptions = null, string $baseBath = null)
	{
		$this->stack->setTrace($trace, $traceOptions, $baseBath);
	}
	
	public function getHTMLTable(): string
	{
		$str = "
		<table cellpadding='0' cellspacing='0' border='0'>
		";
		foreach ($this->stack->toArray() as $name => $val) {
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
		return $this->stack->toArray();
	}
}