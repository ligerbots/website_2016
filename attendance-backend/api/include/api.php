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
define("CALENDAR_GIVEN",	 0b00000100);	// Events that were manually created to give a user credit

//Configuration
$SQL = array(
	"HOST" => DB_HOST,
	"USER" => DB_USER,
	"PASS" => DB_PASSWORD,
	"NAME" => DB_ATTENDANCE_NAME
);

//Global objects
$database;
$user = null;
$from_include = false;

//Connect to the database
$database = new mysqli($SQL["HOST"], $SQL["USER"], $SQL["PASS"], $SQL["NAME"]);
//Check for connection error
if($database->connect_error) {
	error("Failed to connect to database",$database->connect_error);
}

// create db if it doesn't exist
if ($result = $database->query("SHOW TABLES LIKE 'calendar'")) {
    $row_cnt = $result->num_rows;

    $result->close();
    if($row_cnt <= 0) {
        $res = $database->multi_query(file_get_contents(dirname(__FILE__) . "/../sql/attendance.sql"));
        if($res === false) {
        	error("Internal Error", "SQL returned " . $initDbStatement->error);
        }
    }
}

//Check if credentials were supplied
if(isSet($_SERVER['PHP_AUTH_USER'])) {
	//Get global object
	global $user;
	//Get the user
	$user = new User($_SERVER['PHP_AUTH_USER'], USER_SELECTOR_UNAME);
	//Check if the user exists
	if($user->error) {
		//Failure
		generate401("Invalid Credentials1 " . $user->error);
	}
	//Check the password
	if(!$user->checkPassword($_SERVER['PHP_AUTH_PW'])) {
		//Invalid password
		generate401("Invalid Credentials2");
	}
}

function attendanceSetFromInclude() {
	global $from_include;
	$from_include = true;
}

//Error function
function error($message, $detail = "") {
	global $from_include;
	
	if($from_include) {
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
	global $user;
	global $from_include;
	
	if($from_include) { return true; }
	
	//Check if the user even bothered to log in
	if($user==null) {
		//User is not logged in
		generate401("Authentication is required to view the requested resource");
	} else {
		//Check if the user has permission
		if(!$user->checkPermission($permission)) {
			//User does not have permission
			error("Access Denied","Missing required permission " . $permission);
		}
	}
}

//Method for generating a 401 error
function generate401($reason = false) {
	global $from_include;
	if(!$from_include) {
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

?>