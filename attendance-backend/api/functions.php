<?php
// tell the attendance API not to spam out JSON
define("ATTENDANCE_API_INCLUDE", true);
// then load it
include("include/api.php");

function createEvent($user, $start, $end) {
    $database = attendanceGetDatabase();
    
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

function getUserInfo($user) {
    $database = attendanceGetDatabase();
    
    if(!($user instanceof User)) {
        $user = new User($user, USER_SELECTOR_ID);
    }
    
    if(!$user) {
        throw new Exception("No such user");
    }
    
    //Create the statement
    $stmt = $database->prepare(file_get_contents(dirname(__FILE__) . "/sql/getUserInfo.sql"));
    //Bind the parameters
    $stmt->bind_param("iii", $user->udata->id, $user->udata->id, $user->udata->id);
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
    	"id" => $user->udata->id,
    	"fname" => $user->udata->fname,
    	"lname" => $user->udata->lname,
    	"email" => $user->udata->email,
    	"pin" => $user->udata->pin,
    	"rfid" => $user->udata->rfid,
    	"username" => $user->udata->username,
    	"permissions" => $user->udata->permissions,
    	"time" => $object['time'],
    	"abstime" => $object['abstime'],
    	"signedin" => $object['signedin']
    );
    //Return the result
    return $result;
}

function getUsersEvents($user) {
    $database = attendanceGetDatabase();
    
    if(!($user instanceof User)) {
        $user = new User($user, USER_SELECTOR_ID);
    }
    
    if(!$user) {
        throw new Exception("No such user");
    }
    
    //Create the statement
    $stmt = $database->prepare(file_get_contents(dirname(__FILE__) . "/sql/getUsersEvents.sql"));
    //Bind the parameters
    $stmt->bind_param("i",$user->udata->id);
    //Execute the statement
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
    //Get the result
    $qresult = _mysqli_get_result($stmt);
    
    return $qresult;
}
?>