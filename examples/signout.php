<?php

$configFile = dirname(__FILE__) . '/config.php';

if (file_exists($configFile))
{
    include $configFile;
}
else
{
    die("Please rename the config-sample.php file to config.php and add your Findery client id and secret to it\n");
}

spl_autoload_register(function($className)
{
    $className = str_replace ('\\', DIRECTORY_SEPARATOR, $className);
    include (dirname(__FILE__) . '/../src/' . $className . '.php');
});

/**
 * Or use this:
 * `./composer.phar install`
 * require_once dirname(__DIR__) . '/vendor/autoload.php';
 */

use \DPZ\Findery;

$findery = new Findery($clientId, $clientSecret);

$findery->signout();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>DPZFindery Signout Example</title>
        <link rel="stylesheet" href="example.css">
    </head>
    <body>
        <h1>Signed out</h1>
        <p>You have now signed out of this Findery session. <a href="index.php">Sign in</a>.</p>
    </body>
</html>