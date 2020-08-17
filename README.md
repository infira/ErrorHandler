ErrorHandler
=====================

### Comprehensive php error,notice, etc handler.

Once in a while some bug gets into production code what didnt pass unit tests.
ErrorHandler catcehs your defined error levels made by php-core, user, and custom errors and sends it to email, 
logs, stores to file database(coming soon).


Features
--------

 * Emails
 * provide full data for logger
 * send all errors to php system log
 * it provides domprehensive data to debug and fix your code
 * coming soon...Full file based error loging

Setup
-----

Use [composer](http://getcomposer.org) to install the library:

Add the library to your `composer.json` file in your project:

```javascript
{
  "require": {
      "infira/errorhandler": "1.*"
  }
}
```
or terminal


```bash
$ composer require infira/errorhandler
```

### Minimum Requirements
 * PHP 7

Usage
-----

### Add a ErrorHandler to your project
```php
require_once "vendor/autoload.php";
$Handler = new Infira\Error\Handler();
try
{
    require_once "startApp.php";//here you run your entire project
}
catch (\Infira\Error\InfiraError $e)
{
	echo $e->getMessage();
}
catch (Throwable $e)
{
	echo $Handler->catch($e);
}

```

That's it! Your application is catching errors!

### Extended Example
```php
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
: - extra data will be added to error message  

**Return Values**

`void`




**Throws Exceptions**


`\InfiraError`

#### Example
```php
use Infira\Error\Handler AS MyError;
MyError::raise('my custom error');
MyError::raise('my custom error with extra Data',['extra' => 'data']);
```
<hr />

### Handler::raiseEmail  

**Description**

```php
public static raiseEmail (string $message, mixed $extra)
```

Send error to email only, code will continue executing
will work when email is configured
Uses PHPMailer 

 

**Parameters**

* `(string) $message`
* `(mixed) $extra`
: - extra data will be added to error message  

**Return Values**

`void`


#### Example
```php
use Infira\Error\Handler AS MyError;
MyError::raiseEmail('my custom error');
MyError::raiseEmail('my custom error with extra Data',['extra' => 'data']);
```

<hr />

### Handler::addExtraErrorInfo  

**Description**

```php
public static addExtraErrorInfo (string $name, mixed $data)
```

Add extra to error output for more extended information <br>
Will add error output
EXTRA : Array
(
    [extraData] => extra data value
)

 

**Parameters**

* `(string) $name`
* `(mixed) $data`
: - will add to error output  

**Return Values**

`void`

#### Example
```php
use Infira\Error\Handler AS MyError;
MyError::addExtraErrorInfo('extraData','extra Data value');
MyError::raiseEmail('my custom error');
```
<hr />