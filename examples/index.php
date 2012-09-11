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

$path = sprintf('/users/%s/notes', $userId);
$rsp = $findery->call($path, array('limit' => 5));
$notes = $rsp->{'notes'};

?>
<!DOCTYPE html>
<html>
    <head>
        <title>DPZFindery Example</title>
        <link rel="stylesheet" href="example.css">
    </head>
    <body>
        <h1>Findery for <?php echo $userName ?></h1>
        <p><a href="create-note.php">Create a New Note</a></p>
        <ul id="notes">
            <?php foreach ($notes as $note) { ?>
            <?php
                $lat = $note->{'location'}->{'latitude'};
                $lon = $note->{'location'}->{'longitude'};
                $title = $note->{'title'};
                $message = $note->{'message'};
                $image = $note->{'image'};
                $id = $note->{'id'};
                $secret = $note->{'secret'};
            ?>
            <li>
                <span class="title"><?php echo $title ?></span>
                <span class="map">
                    <img src="<?php echo sprintf('http://maps.googleapis.com/maps/api/staticmap?center=+%s,%s&amp;zoom=14&amp;size=300x200&amp;markers=color:blue|%s,%s&amp;sensor=false', $lat, $lon, $lat, $lon) ?>" width="300">
                    <span class="on-findery"><a href="<?php echo sprintf('https://findery.com/notes/%s', $id) ?>">View on Findery</a></span>
                </span>
                <span class="message"><?php echo nl2br($message) ?></span>
                <?php if (!empty($image)) { ?>
                <img class="image" src="<?php echo sprintf('http://images1.findery.com/%s/%s/288xN', $id, $secret) ?>" width="288" ?>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>
        <p><a href="signout.php">Sign out</a></p>
    </body>
</html>

