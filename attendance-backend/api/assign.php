<?php
include("../../wp-backend/wp-config.php");
//Include the API
include("include/api.php");

//Required permission
setAccess("users.modify");

//Get identifiers
$pin = isSet($_GET['pin']) ? $_GET['pin'] : null;
$rfid = isSet($_GET['rfid']) ? $_GET['rfid'] : null;

//Check for invalid request
if($pin == null && $rfid == null) {
	error("Invalid Request", "PIN and RFID serial number must be included in the request");
}

// get the user

$victim;
try {
	//Get the user
	$victim = new User($pin, USER_SELECTOR_PIN);
} catch(Exception $e) {
	error("Invalid User", "No user could be found with the ID provided");
}

// update the rfid tag
update_user_meta($victim->udata->id, "attendance_rfid", $rfid);

success(array("result"=>"success"));
?>