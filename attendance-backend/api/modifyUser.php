<?php
//Include the API
include("include/api.php");

//Required permission
setAccess("users.modify");

//Arguments
$args = [
	"id" => null,
	"pin" => null,
	"rfid" => null,
	"permissions" => null
];

//Get arguments
foreach($args as $name=>&$value) {
	//Set the argument
	if(isSet($_POST[$name])) {
		$value = isSet($_POST[$name]) ? $_POST[$name] : null;
		if($value == "") { $value = null; }
	}
}

//Check the user ID
if($args['id'] == null) {
	error("Invalid Request", "Missing target user ID");
}

$realuser;
try {
	//Get the actual user
	$realuser = new User($args['id'], USER_SELECTOR_ID);
} catch (Exception $e) {
	error("Invalid Request", "Unknown user ID");
}

//Check the permissions
if($args['permissions'] != null) {
	//Check if permissions are valid
	if(json_decode($args['permissions']) == false) {
		error("Invalid Data", "Given permissions were not valid JSON");
	}
	//Get permissions
	$perms = json_decode($permissions);
	//Check each permission
	foreach($perms as $perm) {
		if(!$user->checkPermission($perm)) {
			error("Security Error", "You are not authorized to grant permission \"" . $perm . "\"");
		}
	}
	//Update permissions
	update_user_meta($user->udata->id, "attendance_permissions", $args['permissions']);
}

//Update other fields
if($args['pin']) {
	update_user_meta($user->udata->id, "attendance_pin", $args['pin']);
}
if($args['rfid']) {
	update_user_meta($user->udata->id, "attendance_rfid", $args['rfid']);
}

//Finished
success("Successfuly updated user");
?>