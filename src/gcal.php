<html>
<head></head>
<body>

<?php

include_once '../secret.php';

set_include_path( get_include_path() . PATH_SEPARATOR . '../lib/gapi/src/' );
require_once '../lib/gapi/src/Google/Client.php';
require_once '../lib/gapi/src/Google/Service/Calendar.php';

session_start();

$redirect = 'http://localhost/autoscheduler/src/gcal.php'; // change this later
$client = new Google_Client();
$client->setApplicationName("Google Calendar Event Suggestion");
$client->setClientId($gapi_client_id);
$client->setClientSecret($gapi_client_secret);
$client->setRedirectUri($redirect);
$client->addScope("https://www.googleapis.com/auth/calendar"); 
	// +rw access to calendars. https://www.googleapis.com/auth/calendar.readonly
	// for +r only
$cal = new Google_Service_Calendar($client);

// Access tokens
if (isset($_GET['logout'])) {
	unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token']) && $_SESSION['token']) {
	$client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
	$calList = $cal->calendarList->listCalendarList();
	//print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";
	// grab calendars, present user with form to choose which calendar to use
	// set id and then do a freeBusy query
	$calIdList = array();
	$calListIndex = 0;
	foreach($calList as $calListEntry)
	{
		$calIdList[$calListIndex] = array($calListEntry['summary'], 
			$calListEntry['id']);
		$calListIndex += 1;
	}
	// show user a form of calendar names ('summary') to pick from
	// TODO form here
	
	$_SESSION['token'] = $client->getAccessToken();
} else {
	$authUrl = $client->createAuthUrl();
	print "<a class='login' href='$authUrl'>Connect Me!</a>";
}

?>

</body></html>
