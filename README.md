ErrorHandler
=====================

### Comprehensive php error,notice, etc handler.

Once in a while some bug gets into production what didn't show up in tests.
ErrorHandler catches your defined error levels made by php-core, user, and custom errors and outputs it to browser,
or you can make a custom wrapper to handle erros. Look examples below.


#Install

* Minimum Requirements - PHP 7
Use [composer](http://getcomposer.org) to install the library:

Add the library to your `composer.json` file in your project:

```javascript
{
  "require": {
      "infira/errorhandler": "1.*"
  }
}
```
To use latest and greatest
```javascript
{
  "require": {
      "infira/errorhandler": "dev-master"
  }
}
```
or terminal

```bash
$ composer require infira/errorhandler
```

#Usage

## Custom errors
* will use system default error level
```php
require_once "vendor/autoload.php";
$Handler = new Infira\Error\Handler();
try
{
	alert("my custom error",['extra'=>'data']);
}
catch (\Infira\Error\Error $e)
{
	echo $e->getHTMLTable();
}
catch (Throwable $e)
{
	echo $Handler->catch($e)->getHTMLTable();
}
```
getHTMLTable() will output, with all the goddies server has to offer
![alt text](example.png)

That's it! Your application is catching errors!

### Extended Example
```php
require_once "../vendor/autoload.php";
$config                         = [];
$config['basePath']             = getcwd(); //will replace file path in trace to make more cleaner and safety reasons
$config['errorLevel']           = -1; //will catch all kinds of errors, look https://www.php.net/manual/en/function.error-reporting.php
$config['beforeTrigger']        = function (\Infira\Error\Error $e)
{
	//log my error or to something cool with it
	//if return false, then throw error will be voided
};
$config['debugBacktraceOption'] = DEBUG_BACKTRACE_IGNORE_ARGS; //https://www.php.net/manual/en/function.debug-backtrace.php

$Handler = new \Infira\Error\Handler($config);
try
{
	//... your app code goes here
}
catch (\Infira\Error\Error $e)
{
	echo $e->getHTMLTable();
}
catch (Throwable $e)
{
	echo $Handler->catch($e)->getHTMLTable();
}
```


Class docs
-------

### Handler::raise  

**Description**

```php
public static raise (string $msg, mixed $extra)
```
Raise a error, code will stop executing 
**Parameters**
* `(string) $msg`
* `(mixed) $extra`
: - $extra - extra data will be added to error message  
**Return Values**
`void`
**Throws Exceptions**
`\Infira\Error\Error`

#### Example
```php
\Infira\Error\Handler::raise('my custom error');
\Infira\Error\Handler::raise('my custom error with extra Data',['extra' => 'data']);
```
<hr />

### Handler::addExtraErrorInfo  

**Description**

```php
public static addExtraErrorInfo (string|array $name, mixed $data)
```
Add extra to error output for more extended information 
**Parameters**
* `(string|array) $name`
: - string, or in case of array ,every key will be added as extra data key to error output  
* `(mixed) $data`
: - [$name=>$data] will be added to error output  
**Return Values**
`void`
<hr />

#### Example
```php
\Infira\Error\Handler::addExtraErrorInfo('extraData','extra Data value');
```