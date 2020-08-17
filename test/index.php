<?php
require_once "../vendor/autoload.php";
$Mailer = new PHPMailer\PHPMailer\PHPMailer();
$Mailer->addAddress('gen@infira.ee');
$Mailer->setFrom('beta@infira.ee');
$Mailer->Subject                = 'My beta site error';
$config                         = [];
$config['errorLevel']           = -1;
$config['mailer']               = $Mailer;
$config['beforeThrow']          = function (\Infira\Error\Node $Node)
{
	var_dump($Node->getVars());
};
$config['debugBacktraceOption'] = 0;

$Handler = new \Infira\Error\Handler($config);


try
{
	addExtraErrorInfo("extraData", "extra data value");
	raiseSomeError();
}
catch (\Infira\Error\InfiraError $e)
{
	echo $e->getMessage();
}
catch (Throwable $e)
{
	echo $Handler->catch($e);
}