<?php
include("../../wp-backend/wp-config.php");
//Include the API
include("include/api.php");

//Required permission
setAccess("users.getinfo");

$users = get_users();
$students = array();
$allEvents = array();
foreach($users as $user) {
  if(!is_array($user->team_role)) {
    continue;
  }
  if(in_array("Student", $user->team_role)) {
    $database = attendanceGetDatabase();
    $id = $user->id;
    
    // get total time signed in
    $stmt = $database->prepare(file_get_contents(dirname(__FILE__) . "/sql/getUserInfo.sql"));
    //Bind the parameters
    $stmt->bind_param("iii", $id, $id, $id);
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
    $totalTime = $object['time'];
    
    // get all events for user
    $stmt = $database->prepare(file_get_contents(dirname(__FILE__) . "/sql/getUsersEvents.sql"));
    //Bind the parameters
    $stmt->bind_param("i",$id);
    //Execute the statement
    if($stmt->execute() === false) {
    	error("Internal Error","SQL returned " . $stmt->error);
    }
    //Get the result
    $qresult = _mysqli_get_result($stmt);
    
    $data = array(
        "name" => $user->first_name . " " . $user->last_name,
        "total_hours" => $totalTime,
        "total_meetings" => sizeof($qresult)
    );
    foreach($qresult as $i=>$row) {
        if($row['meta'] & CALENDAR_SUSPENDED) {
            continue;
        }
        $start = intval($row['start']);
        $length = intval($row['end']) - $start;
        if(!in_array($start, $allEvents)) {
            $allEvents[] = $start;
        }
        $data[$start] = $length;
    }
    $students[] = $data;
  }
}

$formattedData = array();
sort($allEvents);
foreach($students as $student) {
    $row = array(
        "name" => $student['name'],
        "total_meetings" => $student['total_meetings'],
        "total_hours" => floor(($student["total_hours"])/360)/10
    );
    foreach($allEvents as $eventTs) {
        $columnHeader = date("m/d/y", $eventTs);
        if(isset($student[$eventTs])) {
            $hours = floor(($student[$eventTs])/360)/10;
            if(isset($row[$columnHeader])) {
                $row[$columnHeader] += $hours;
            } else {
                $row[$columnHeader] = $hours;
            }
        } else if(!isset($row[$columnHeader])) {
            $row[$columnHeader] = "";
        }
    }
    $formattedData[] = $row;
}

function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

// filename for download
$filename = "attendance_data_" . date('Ymd') . ".csv";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv");

$flag = false;
foreach($formattedData as $row) {
    if(!$flag) {
        // display field/column names as first row
        echo implode(",", array_keys($row)) . "\r\n";
        $flag = true;
    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    echo implode(",", array_values($row)) . "\r\n";
}
?>