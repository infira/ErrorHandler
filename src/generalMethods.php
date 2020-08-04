<?php
function alertEmail($msg)
{
	\Infira\Error\Handler::alertEmail($msg);
}

/**
 * Triggers a E_USER_ERROR
 *
 * @param string $msg
 */
function alert(string $msg)
{
	\Infira\Error\Handler::setTrace(debug_backtrace(\Infira\Error\Handler::$debugBacktraceOption));
	\Infira\Error\Handler::trigger($msg, E_USER_ERROR);
}

function clearExtraErrorInfo()
{
	$GLOBALS["extraErrorInfo"] = [];
}

function addExtraErrorInfo($name, $data = false)
{
	if (!isset($GLOBALS["extraErrorInfo"]))
	{
		$GLOBALS["extraErrorInfo"] = [];
	}
	if (is_array($name) and $data === false)
	{
		foreach ($name as $n => $v)
		{
			addExtraErrorInfo($n, $v);
		}
	}
	else
	{
		$GLOBALS["extraErrorInfo"][$name] = $data;
	}
}