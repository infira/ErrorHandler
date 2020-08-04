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

Add the library to your `composer.json` file in your project:

```javascript
{
  "require": {
      "infira/errorhandler": "1.*"
  }
}
```

Use [composer](http://getcomposer.org) to install the library:

```bash
$ composer require infira/errorhandler
```

Composer will install SimpleLog inside your vendor folder. Then you can add the following to your
.php files to use the library with Autoloading.

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
catch (\Infira\Error\Error $e)
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
require_once "vendor/autoload.php";

$Mailer = new PHPMailer\PHPMailer\PHPMailer();
$Mailer->addAddress("gen@infira.ee");
$Mailer->setFrom("beta@infira.ee");
$Mailer->Subject = "My beta site error";
$config = [
    "errorLevel" => -1,//-1 means all erors, see https://www.php.net/manual/en/function.error-reporting.php
    "email" => $Mailer, //or you can provide simply email address, then default PHPMailer instance is used

    /*
     * Provide a calllable argument for loging, use your own logger
     * For example https://github.com/markrogoyski/simplelog-php/blob/master/README.md
     */
    "log" => function (\Infira\Error\Node $Node)
    {
        //var_dump($Node->getVars());
    },
    "debugBacktraceOption" => 0 //Disable 'object' key from trace for less footprint https://stackoverflow.com/questions/12245975/how-to-disable-object-providing-in-debug-backtrace
];
$Handler = new \Infira\Error\Handler($config);

try
{
    require_once "startApp.php";
}
catch (\Infira\Error\Error $e)
{
	echo $e->getMessage();
}
catch (Throwable $e)
{
	echo $Handler->catch($e);
}

```

License
-------

ErrorHandler is licensed under the MIT License.