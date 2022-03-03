<?php

use Infira\Error\Handler;

require_once "../vendor/autoload.php";
Handler::register();
try {
	\Infira\Error\Error::addDebug('global debug data', 'random string');
	asdasds("trigger_error");
	exit;
	addExtraErrorInfo('more', ['value1', 'value2']);
	alert('my custom error', ['extra' => 'data']);
	exit;
	//echo $aas;// addExtraErrorInfo("extraData", "extra data value");
	throw new Exception('throw new ExceptionHandler');
	//\Infira\ErrorException\Handler::raise("Raise infira error");
	//raiseSomeError();
	//trigger_error("error");
}
catch (Throwable $e) {
	echo "<pre>";
	echo print_r(Handler::compile($e)->toArray());
}