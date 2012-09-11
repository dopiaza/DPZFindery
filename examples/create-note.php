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

if (!empty($_POST))
{
    $title = $_POST['title'];
    $text = $_POST['text'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $path = '/notes';

    $param = array(
        'title' => $title,
        'message' => $text,
        'visibility' => 'public',
        'location[latitude]' => $latitude,
        'location[longitude]' => $longitude,
        'tags[]' => 'DPZFindery'
    );

    $photoDetails = $_FILES['photo'];

    if ($photoDetails['size'] > 0)
    {
        $param['image_file'] = '@' . $photoDetails['tmp_name'];
    }

    $rsp = $findery->call($path, $param, 'POST');

    $status = @$rsp->{'status'};

    if ($status == 'error')
    {
        $error = @$rsp->{'message'};
    }
    else
    {
        // Note was created - go to the main page to view it
        $redirectTo = sprintf('%s://%s%s%s/index.php',
            (@$_SERVER['HTTPS'] == "on") ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            ($_SERVER['SERVER_PORT'] == 80) ? '' : ':' . $_SERVER['SERVER_PORT'],
            $redirectPathPrefix
        );

        header("Location: $redirectTo");

        exit(0);
    }

}


?>
<!DOCTYPE html>
<html>
<head>
    <title>DPZFindery Create Note Example</title>
    <link rel="stylesheet" href="example.css">
</head>
<body>
<h1>Findery for <?php echo $userName ?></h1>

<h2>Create a Note</h2>

<p><a href="index.php">View Notes</a></p>

<?php if (!empty($error)) { ?>
    <p class="error"><?php echo $error ?></p>
<?php } ?>

<form id="create-note" method="POST" enctype="multipart/form-data">
    <label for="title">Title</label>
    <input id="title" name="title" type="text" size="50">

    <label for="text">Text</label>
    <textarea id="text" name="text" rows="10" cols="50"></textarea>

    <label for="latitude">Latitude</label>
    <input id="latitude" name="latitude" type="text" size="20">

    <label for="longitude">Longitude</label>
    <input id="longitude" name="longitude" type="text" size="20">

    <a id="locate-me" href="#" onclick="getLocation();return false;">Find my location</a>
    <span id="location-error"></span>


    <label for="photo">Attach a photo</label>
    <input id="photo" name="photo" type="file">

    <input id="create-note" type="submit" value="Create Note">
</form>
<p><a href="signout.php">Sign out</a></p>

<script language="javascript">
    function getLocation()
    {
        if (navigator.geolocation)
        {
            navigator.geolocation.getCurrentPosition(function(position)
            {
                var lat = document.getElementById('latitude');
                var lon = document.getElementById('longitude');
                lat.value = position.coords.latitude;
                lon.value = position.coords.longitude;
            },
            function(error)
            {
                var errorElement = document.getElementById('location-error');
                switch(error.code)
                {
                    case error.PERMISSION_DENIED:
                        errorElement.innerHTML="Nope, you're not allowed to do that."
                        break;

                    case error.POSITION_UNAVAILABLE:
                        errorElement.innerHTML="I'm not sure where we are!"
                        break;

                    case error.TIMEOUT:
                        errorElement.innerHTML="Sorry, got bored of waiting."
                        break;

                    case error.UNKNOWN_ERROR:
                        errorElement.innerHTML="Hmm, something went wrong there..."
                        break;
                }
            });
        }
        else
        {
            x.innerHTML="Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position)
    {
        x.innerHTML="Latitude: " + position.coords.latitude +
            "<br />Longitude: " + position.coords.longitude;
    }
</script>
</body>
</html>

