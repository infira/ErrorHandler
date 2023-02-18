<?php

use Infira\Error\Handler;
use Infira\Error\Error;

require_once "../vendor/autoload.php";
Handler::register([
    'errorLevel' => -1,
    'dateFormat' => 'd.m.Y H:i:s'
]);
try {
    Error::capsule(function () {
        Error::setDebug(['blaah' => "asdasd"]);
        //none
    });

    throw Error::RuntimeException('aasd')->withDebug([
        'global debug data' => 'random string'
    ]);


    Error::setDebug('global debug data', 'random string');
    asdasds("trigger_error");
    exit;
    addExtraErrorInfo('more', ['value1', 'value2']);
    alert('my custom error', ['extra' => 'data']);
    exit;
    //echo $aas;// addExtraErrorInfo("extraData", "extra data value");
    throw new Exception('error');
    //\Infira\ErrorException\Handler::raise("Raise infira error");
    //raiseSomeError();
    //trigger_error("error");
}
catch (Throwable $e) {
    echo "<pre>";
    echo print_r(Handler::compile($e)->toArray());
}