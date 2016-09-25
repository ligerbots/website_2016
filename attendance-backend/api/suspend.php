<?php
include(dirname(__FILE__) . "/../../wp-backend/wp-config.php");
//Include the API
include("include/api.php");

// allow to be called from cron only
if(php_sapi_name() !== "cli") {
    error("This script can only be run from cron");
}

// check all events, suspend and close all that are not signed out
$sqlQuery = "UPDATE `calendar` SET `meta`=CHAR(ORD(`meta`) | ?), `end`=`start` WHERE `end`=0";
$stmt = $_attendance_database->prepare($sqlQuery);
$param = CALENDAR_SUSPENDED;
$stmt->bind_param("i", $param);
if(!$stmt->execute()) {
	//Failure
	throw new Exception("MySQL error: " . $stmt->error);
} else {
    echo "Success\n";
}
?>