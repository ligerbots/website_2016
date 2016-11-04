<?php
//Disable printing of errors
error_reporting(-1);
//Check for error reporting override
if(isSet($_GET['debug'])) { error_reporting(intval($_GET['debug'])); }

//Include dependencies
include("user.php");

//Define calendar meta values
define("CALENDAR_SUSPENDED", 0b00000001);	// Events that are suspended will not count towards total time
define("CALENDAR_MODIFIED",	 0b00000010);	// Events that were changed from their original values
define("CALENDAR_GIVEN",     0b00000100);	// Events that were manually created to give a user credit

if(!defined('ATTENDANCE_API_INCLUDE')) {
	define('ATTENDANCE_API_INCLUDE', false);
}

//Global objects
$_attendance_database = null;
$_attendance_user = null;

function attendanceGetDatabase() {
	global $_attendance_database;
	return $_attendance_database;
}

function attendanceInit() {
	global $_attendance_database;
	
	if($_attendance_database) return;
	
	//Connect to the database
	$_attendance_database = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_ATTENDANCE_NAME);
	//Check for connection error
	if($_attendance_database->connect_error) {
		error("Failed to connect to database", $_attendance_database->connect_error);
	}
	
	// create db if it doesn't exist
	if ($result = $_attendance_database->query("SHOW TABLES LIKE 'calendar'")) {
	    $row_cnt = $result->num_rows;
	
	    $result->close();
	    if($row_cnt <= 0) {
	        $res = $_attendance_database->multi_query(file_get_contents(dirname(__FILE__) . "/../sql/attendance.sql"));
	        if($res === false) {
	        	error("Internal Error", "SQL returned " . $initDbStatement->error);
	        }
	    }
	}
	
	//Check if credentials were supplied
	if(isSet($_SERVER['PHP_AUTH_USER'])) {
		//Get global object
		global $_attendance_user;
		//Get the user
		try {
			$_attendance_user = new User($_SERVER['PHP_AUTH_USER'], USER_SELECTOR_UNAME);
			
			//Check the password
			if(!$_attendance_user->checkPassword($_SERVER['PHP_AUTH_PW'])) {
				//Invalid password
				generate401("Invalid Credentials2");
			}
		} catch(Exception $e) {
			generate401("Invalid Credentials1 " . $user->error);
		}
	}
}

//Error function
function error($message, $detail = "") {
	if(ATTENDANCE_API_INCLUDE) {
		throw new Exception("$message\n$detail");
	}
	// otherwise
	
	//Create the response object
	$response = array();
	//Set the result information
	$response["result"] = "error";
	$response["message"] = $message;
	//Add detail if specified
	if($detail !== false) {	$response["detail"] = $detail; }
	//Set headers
	header("Content-Type: text/plain");
	//Return the message
	die(json_encode($response, JSON_PRETTY_PRINT));
}

//Success function
function success($response) {
	//Set headers
	header("Content-Type: text/plain");
	//Check the type
	if(gettype($response) != "object" && gettype($response) != "array") {
		$response = array("result" => "success", "message" => $response);
	}
	//Encode and return
	die(json_encode($response, JSON_PRETTY_PRINT));
}

//Method for setting the required authentication and permission
function setAccess($permission) {
	//Get global object
	global $_attendance_user;
	
	if(ATTENDANCE_API_INCLUDE) { return true; }
	
	//Check if the user even bothered to log in
	if($_attendance_user==null) {
		//User is not logged in
		generate401("Authentication is required to view the requested resource");
	} else {
		//Check if the user has permission
		if(!$_attendance_user->checkPermission($permission)) {
			//User does not have permission
			error("Access Denied","Missing required permission " . $permission);
		}
	}
}

//Method for generating a 401 error
function generate401($reason = false) {
	if(!ATTENDANCE_API_INCLUDE) {
		//Send headers
		header('WWW-Authenticate: Basic realm="Ligerbots Attendance"');
		header('HTTP/1.0 401 Unauthorized');
	}
	//Message
	error("Authentication Failure",$reason);
}

// http://stackoverflow.com/questions/10752815/mysqli-get-result-alternative
// replaces get_result which isn't available on Arvixe
function _mysqli_get_result( $Statement ) {
    $RESULT = array();
    $Statement->store_result();
    for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
        $Metadata = $Statement->result_metadata();
        $PARAMS = array();
        while ( $Field = $Metadata->fetch_field() ) {
            $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
        }
        call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
        $Statement->fetch();
    }
    return $RESULT;
}

attendanceInit();

?>