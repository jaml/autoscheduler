<html>
<head></head>
<body>

<?php

include_once '../secret.php';

require_once '../lib/gapi/src/Google/Client.php';
require_once '../lib/gapi/src/Google/Service/Calendar.php';

// see: https://github.com/google/google-api-php-client/blob/master/examples/fileupload.php
$redirect = 'http://localhost:8080/gcal.php'; // change this later
$client = new Google_Client();
$client->setApplicationName("Google Calendar Event Suggestion");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect);
$client->addScope("https://www.googleapis.com/auth/calendar"); 
	// +rw access to calendars. https://www.googleapis.com/auth/calendar.readonly
	// for +r only

// Access tokens
// from: https://github.com/google/google-api-php-client/blob/master/examples/user-example.php
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
/************************************************
  If we have a code back from the OAuth 2.0 flow,
  we need to exchange that with the authenticate()
  function. We store the resultant access token
  bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}
/************************************************
  If we have an access token, we can make
  requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $authUrl = $client->createAuthUrl();
}

$calserv = new Google_Service_Calendar($client);
//TODO: grab list of user's owned calendars. Let user pick which one to use.

$events = $calserv->events->listEvents('primary'); //change this later

while(true) {
	foreach ($events->getItems() as $event) {
				echo $event->getSummary();
					}
}
?>

</body></html>
