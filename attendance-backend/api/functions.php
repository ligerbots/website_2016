<?php
include("include/api.php");
// make sure the api doesn't print out JSON and die() unexpectedly
attendanceSetFromInclude();

function createEvent($user, $start, $end) {
    global $database;
    
    //Check for error
    if($user == null || $start == null || $end == null) {
    	error("Missing required parameters");
    }
    
    //Create statement
    $stmt = $database->prepare("INSERT INTO calendar (start,end,user,meta) VALUES (?,?,?,b'00000100')");
    //Bind parameters
    $stmt->bind_param("iii", $start, $end, $user);
    //Execute the query
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
}

function createUser($wp_id, $fname, $lname, $email) {
    global $database;
    
    // generate PIN
    $found_pin = false;
    $pin;
    do {
        $pin = mt_rand(1, 9999);
        
        //Check if the pin is unique
        $fakeuser = new User($pin, USER_SELECTOR_PIN);
        if($fakeuser->error !== false) {
        	$found_pin = true;
        }
    } while(!$found_pin);
    $pin = sprintf("%04d", $pin);
    
    // most are unimplemented dummy args right now
    $args = [
    	"fname" => $fname,
    	"lname" => $lname,
    	"email" => $email,
    	"pin" => $pin,
    	"rfid" => "",
    	"username" => $wp_id,
    	"password" => "",
    	"permissions" => "[]"
    ];

    
    //Create the statement
    $stmt = $database->prepare("INSERT INTO users (fname,lname,email,pin,rfid,username,passhash,permissions) VALUES (?,?,?,?,?,?,?,?)");
    //Bind the parameters
    $stmt->bind_param("ssssssss", $args['fname'],$args['lname'],$args['email'],$args['pin'],$args['rfid'],$args['username'],$args['password'],$args['permissions']);
    //Execute the query
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
}

function getUserIdByWpId($wp_id) {
    global $database;
    
    $stmt = $database->prepare("SELECT `id` FROM `users` WHERE `username`=?");
    //Bind the parameters
    $stmt->bind_param("s", $wp_id);
    //Execute the statement
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
    //Get the result
    $qresult = _mysqli_get_result($stmt);
    //Check for error
    if(sizeof($qresult) == 0) {
    	error("Invalid User", "No user found with that ID");
    }
    return $qresult[0]['id'];
}

function getUserInfo($wp_id) {
    global $database;
    
    $id = getUserIdByWpId($wp_id);
    //Create the statement
    $stmt = $database->prepare(file_get_contents(dirname(__FILE__) . "/sql/getUserInfo.sql"));
    //Bind the parameters
    $stmt->bind_param("i",$id);
    //Execute the statement
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
    //Get the result
    $qresult = _mysqli_get_result($stmt);
    //Check for error
    if(sizeof($qresult) == 0) {
    	error("Invalid User", "No user found with that ID");
    }
    
    $object = $qresult[0];
    
    //Create the end result
    $result = array(
    	"id" => $object['id'],
    	"fname" => $object['fname'],
    	"lname" => $object['lname'],
    	"email" => $object['email'],
    	"pin" => $object['pin'],
    	"rfid" => $object['rfid'],
    	"username" => $object['username'],
    	"permissions" => json_decode($object['permissions']),
    	"time" => $object['time'],
    	"abstime" => $object['abstime'],
    	"signedin" => $object['signedin']
    );
    //Return the result
    return $result;
}

function getUsersEvents($wp_id) {
    global $database;
    
    $id = getUserIdByWpId($wp_id);
    
    //Create the statement
    $stmt = $database->prepare(file_get_contents(dirname(__FILE__) . "/sql/getUsersEvents.sql"));
    //Bind the parameters
    $stmt->bind_param("i",$id);
    //Execute the statement
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
    //Get the result
    $qresult = _mysqli_get_result($stmt);
    //Result
    $result = array();
    //Process result
    while(($row = array_shift($qresult)) != NULL) {
    	//Create the event object
    	$event = [];
    	//Set the user data
    	$event['id'] = $row['id'];
    	$event['user'] = $row['user'];
    	$event['start'] = $row['start'];
    	$event['end'] = $row['end'];
    	$event['meta'] = dechex($row['meta']);
    	$event['isopen'] = $row['isopen'];
    	$event['name'] = $row['name'];
    	//Push to the list
    	array_push($result, $event);
    }
    
    return $result;
}
?>