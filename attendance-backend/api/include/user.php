<?php

//ID types
define("USER_SELECTOR_ID",      0b00000001);
define("USER_SELECTOR_PIN",     0b00000010);
define("USER_SELECTOR_RFID",    0b00000100);
define("USER_SELECTOR_UNAME",   0b00001000);
define("USER_SELECTOR_EMAIL",   0b00010000);
define("ATTENDANCE_SIGNED_IN",  "signedIn");
define("ATTENDANCE_SIGNED_OUT", "signedOut");

//User class
class User {
	//Data
	var $udata = false;
	
	//User constructor
	function __construct($identifier, $idtype) {
		$wp_user = false;
		if($idtype & USER_SELECTOR_ID) {
			$wp_user = new WP_User($identifier);
		} else if($idtype & USER_SELECTOR_EMAIL) {
			$wp_user = get_user_by('email', $identifier);
		} else if($idtype & USER_SELECTOR_UNAME) {
			$wp_user = get_user_by('login', $identifier);
		} else if($idtype & USER_SELECTOR_PIN || $idtype & USER_SELECTOR_RFID) {
			$params = array('meta_key' => ($idtype & USER_SELECTOR_PIN) ? 'attendance_pin' : 'attendance_rfid', 'meta_value' => $identifier);
			$user_arr = get_users($params);
			if(sizeof($user_arr) > 0) {
				$wp_user = $user_arr[0];
			}
		}
		
		if(!$wp_user || $wp_user->id == 0) {
			throw new Exception("No such user: " . $identifier);
		}
		
		//Create the query to check if signed in
		$query = "SELECT IF(calendar.end IS NOT NULL,'1','0') AS 'signedin' FROM calendar WHERE calendar.user=? AND calendar.end=0";
		//Get global object
		$database = attendanceGetDatabase();
		//Get the database statement
		$stmt = $database->prepare($query);
		if(!$stmt) {
			throw new Exception("SQL error: " . $database->error);
		}
		$id = $wp_user->id;
		$stmt->bind_param('i', $id);
		//Execute the statement
		if(!$stmt->execute()) {
			throw new Exception("SQL error: " . $stmt->error);
		}
		//Fetch user information
		$result = _mysqli_get_result($stmt);
		
		$this->udata = new stdClass();
		
		if(sizeof($result) != 0) {
			$this->udata->signedin = $result[0]['signedin'] == "1";
		} else {
			$this->udata->signedin = false;
		}
		
		$this->wp_user = $wp_user;
		
		$this->udata->fname = $wp_user->first_name;
		$this->udata->lname = $wp_user->last_name;
		$this->udata->id = $wp_user->id;
		$this->udata->email = $wp_user->user_email;
		$this->udata->pin = $wp_user->attendance_pin;
		$this->udata->rfid = $wp_user->attendance_rfid;
		$this->udata->username = $wp_user->user_login;
		$this->udata->passhash = $wp_user->user_pass;
		$this->udata->permissions = json_decode($wp_user->attendance_permissions);
		if(!$this->udata->permissions) {
			$this->udata->permissions = array();
		}
		
		// make sure we have a pin
		if($wp_user->attendance_pin == "" || strlen($wp_user->attendance_pin) < 4) {
			// guess not; time to create a pin
			$pin = false;
			while(!$pin) {
				$pin = mt_rand(1000, 9999);
				// check if a user already has that pin
				try {
					new User($pin, USER_SELECTOR_PIN);
				} catch(Exception $e) {
					// all good
				}
			}
			update_user_meta($wp_user->id, "attendance_pin", $pin);
			$this->udata->pin = $pin;
		}
	}

	//Method for checking a password
	function checkPassword($password) {
		//Return the password verification result
		return wp_check_password($password, $this->udata->passhash, $this->udata->id);
	}

	//Method for getting user permissions
	function getPermission() {
		//Return decoded permission
		return $this->udata->permissions;
	}

	//Method for checking the user permission
	function checkPermission($permission) {
		//Check for super admin
		if($this->isSuperAdmin()) { return true; }
		//Check the permission
		return in_array($permission, $this->getPermission());
	}

	//Method for checking if the user is a super admin
	function isSuperAdmin() {
		return user_can($this->udata->id, 'edit_posts');
	}

	//Method for toggling the user state
	function signToggle() {
		//Check the current state
		if($this->udata->signedin == "1") {
			//Sign the user out
			return $this->signOut();
		} else {
			//Sign the user in
			return $this->signIn();
		}
	}

	//Method for signing the user out
	function signOut() {
		//Get the global database object
		global $_attendance_database;
		//Time variable (because something about statment bind security)
		$time = time();
		//Create the statement
		$stmt = $_attendance_database->prepare("UPDATE calendar SET end=? WHERE user=? AND end=0 LIMIT 1");
		//Bind the parameters
		$stmt->bind_param("ii", $time, $this->udata->id);
		//Execute the query
		if(!$stmt->execute()) {
			//Failure
			return $stmt->error;
		}
		//Finished
		return ATTENDANCE_SIGNED_OUT;
	}

	//Method for signing the user in
	function signIn() {
		//Get the global database object
		global $_attendance_database;
		//Time variable (because something about statment bind security)
		$time = time();
		//Create the statement
		$stmt = $_attendance_database->prepare("INSERT INTO calendar (start,end,user,meta) VALUES (?,0,?,b'00000000')");
		//Bind the parameters
		$stmt->bind_param("ii", $time, $this->udata->id);
		//Execute the query
		if(!$stmt->execute()) {
			//Failure
			return $stmt->error;
		}
		//Finished
		return ATTENDANCE_SIGNED_IN;
	}
}

?>