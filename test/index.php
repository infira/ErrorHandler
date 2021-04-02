<?php
require_once "../vendor/autoload.php";
$Mailer = new PHPMailer\PHPMailer\PHPMailer();
$Mailer->addAddress('gen@infira.ee');
$Mailer->setFrom('beta@infira.ee');
$Mailer->Subject                = 'My site error';
$config                         = [];
$config['errorLevel']           = -1;
$config['email']                = $Mailer;
$config['debugBacktraceOption'] = 0;

$Handler = new \Infira\Error\Handler($config);

try
{
	addExtraErrorInfo("extraData", "extra data value");
	throw new Exception('throw new Exception');
	//\Infira\Error\Handler::raise("Raise infira error");
	//raiseSomeError();
	//trigger_error("error");
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