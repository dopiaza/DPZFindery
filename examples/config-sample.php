<?php

// Edit the lines below to add your Findery client id and secret

$clientId = 'PUT_YOUR_FINDERY_CLIENT_ID_KEY_HERE';
$clientSecret = 'PUT_YOUR_FINDERY_CLIENT_SECRET_HERE';

// You will need to configure the redirect URI over on Findery for this client id. To run this example, you need
// to set it to http://whatever.my.server.is.com/path/to/DPZFindery/auth.php


// The example code needs to know a little bit about how your web server is configured so that it can work out the
// correct URL to generate for redirects.
// If you have a virtual server with this example code running in the document root (with a URL something like
// http://dpzfindery.local/), then you can leave $redirectPathPrefix set to an empty string.
// If, however, your running in a sub-directory (something like http://my.web.server.com/stuff/dpzfindery/index.php),
// then you need to set this to the path to DPZFindery (in this example, '/stuff/dpzfindery'). Do not include any
// trailing slashes.

$redirectPathPrefix = '/examples';