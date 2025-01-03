<?php
include("../../wp-backend/wp-config.php");
//Include the API
include("include/api.php");

//Required permission
setAccess("event.trigger");

//Get identifiers
$pin = isSet($_GET['pin']) ? $_GET['pin'] : null;
$rfid = isSet($_GET['rfid']) ? $_GET['rfid'] : null;

//Check for invalid request
if($pin == null && $rfid == null) {
	error("Invalid Request", "Either a PIN or RFID serial number must be included in the request");
}
if($pin != null && $rfid != null) {
	error("Invalid Request", "Use a PIN or RFID serial number, but not both");
}

//Get the means of identification
$id = "";
$selector = 0;
//Determine the ID and selector
if($pin != null) {
	$id = $pin;
	$selector = USER_SELECTOR_PIN;
} else {
	$id = $rfid;
	$selector = USER_SELECTOR_RFID;
}

$victim;
try {
	//Get the user
	$victim = new User($id, $selector);
} catch(Exception $e) {
	error("Invalid User", "No user could be found with the ID provided");
}

//Trigger the user.
$result = $victim->signToggle();
//Check for error
if($result !== "signedIn" && $result !== "signedOut") {
	//Error
	error("Failed to trigger user", "Internal error: " . $result);
}

//Display result
if($result == ATTENDANCE_SIGNED_OUT) {
	success(array("result"=>"success","state"=>"0","signed_in"=>false,"message"=>"Goodbye, " . $victim->udata->fname));
} else {
	success(array("result"=>"success","state"=>"1","signed_in"=>true,"message"=>"Hello, " . $victim->udata->fname));
}
?>