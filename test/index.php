<?php
require_once "../vendor/autoload.php";
$config                         = [];
$config['basePath']             = getcwd();
$config['errorLevel']           = -1;
$config['debugBacktraceOption'] = DEBUG_BACKTRACE_IGNORE_ARGS;

$Handler = new \Infira\Error\Handler($config);

try
{
	addExtraErrorInfo('more', ['value1', 'value2']);
	//echo $aas;// addExtraErrorInfo("extraData", "extra data value");
	throw new Exception('throw new Exception');
	//\Infira\Error\Handler::raise("Raise infira error");
	//raiseSomeError();
	//trigger_error("error");
	alert('my custom error', ['extra' => 'data']);
}
catch (\Infira\Error\Error $e)
{
	debug("catch infira error");
	echo $e->getHTMLTable();
}
catch (Throwable $e)
{
	debug("Catch Throwable");
	echo $Handler->catch($e)->getHTMLTable();
}
?>