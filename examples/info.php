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

// We need to set up the callback for the authentication process - this must match the redirect URI set up for this
// client id on Findery. For this example, the redirect uri must point at our auth.php script.
$callback = sprintf('%s://%s%s%s/auth.php',
    (@$_SERVER['HTTPS'] == "on") ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    ($_SERVER['SERVER_PORT'] == 80) ? '' : ':' . $_SERVER['SERVER_PORT'],
    $redirectPathPrefix
);

$findery = new Findery($clientId, $clientSecret, $callback);


if (!$findery->authenticate('read notes'))
{
    die("Hmm, something went wrong...\n");
}

$userId = $findery->getOauthData(\DPZ\Findery::USER_ID);
$userName = $findery->getOauthData(\DPZ\Findery::USER_NAME);
$accessToken = $findery->getOauthData(\DPZ\Findery::OAUTH_ACCESS_TOKEN);

?>
<!DOCTYPE html>
<html>
<head>
    <title>DPZFindery Example</title>
    <link rel="stylesheet" href="example.css">
</head>
<body>
<h1>Findery for <?php echo $userName ?></h1>
<p><a href="index.php">View Notes</a></p>
<p>
    User: <?php echo $userId ?><br>
    Name: <?php echo $userName ?><br>
    Token: <?php echo $accessToken ?><br>
</p>
<p><a href="signout.php">Sign out</a></p>
</body>
</html>

