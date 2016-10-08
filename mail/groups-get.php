<?php
require_once 'google-api-php-client-2.0.2/vendor/autoload.php';

$keyFile = "/home/frc2877/private/auth-key.json"; // path to key file goes here
$actAs = "erik_uhlmann@ligerbots.com";
$team_directory_id = "1RKZZs-qmGoOHs3vRV6ZAPtOumNTmyFbQx_Nj762A1Uc";
$team_directory_range = "Active!A1:AC1981";
$incoming_members_id = "1oX90NbOujzilSnhUPnW0Tctgaoe_SlVBfTVBs-4MOww";
$incoming_members_range = "ALL LISTS SORTED!A1:AA988";

// load Google API

putenv("GOOGLE_APPLICATION_CREDENTIALS=$keyFile");

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(Google_Service_Directory::ADMIN_DIRECTORY_GROUP);
$client->addScope(Google_Service_Sheets::SPREADSHEETS);
$client->setSubject($actAs);
$dir = new Google_Service_Directory($client);

header("Content-Type: text/plain");

$members = array();

function scanMembers($group, $overrideName) {
  global $dir;
  global $members;

  $list = $dir->members->listMembers($group)->getMembers();
  foreach($list as $member) { 
    $email = $member->email;
    if(array_key_exists($email, $members)) continue;
    $memberInfo = array("email" => $email);
    if($overrideName !== FALSE) {
      $memberInfo["name-override"] = $overrideName;
    }
    $members[$email] = $memberInfo;
  }
}

// parents_south@ligerbots.com
// parents_north@ligerbots.com
// coaches@ligerbots.com
// mentors_other@ligerbots.com
// students_south@ligerbots.com
// students_north@ligerbots.com

if(isset($_REQUEST['parents_south'])) scanMembers("parents_south@ligerbots.com", "parents");
if(isset($_REQUEST['parents_north'])) scanMembers("parents_north@ligerbots.com", "parents");
if(isset($_REQUEST['coaches'])) scanMembers("coaches@ligerbots.com", "coaches");
if(isset($_REQUEST['mentors_other'])) scanMembers("mentors_other@ligerbots.com", "mentors");
if(isset($_REQUEST['students_south'])) scanMembers("students_south@ligerbots.com", FALSE);
if(isset($_REQUEST['students_north'])) scanMembers("students_north@ligerbots.com", FALSE);

//var_dump($members);

$sheets = new Google_Service_Sheets($client);
$team_directory = $sheets->spreadsheets_values->get($team_directory_id, $team_directory_range)->getValues();
$headers = $team_directory[0];
// make sure we don't get problems if somebody changes something
if(!($headers[0] == 'Firstname' && $headers[1] == 'Lastname' && $headers[2] == 'Email')) {
  error_log("Team Directory Spreadsheet changed!");
  http_response_code(500);
  die();
}

foreach($team_directory as $i=>$row) {
  if($i == 0) continue;
  $nameFirst = $row[0];
  $nameLast = $row[1];
  $email = $row[2];
  if(array_key_exists($email, $members)) {
    $members[$email]['name-first'] = $nameFirst;
    $members[$email]['name-last'] = $nameLast;
  }
}

if(isset($_REQUEST['incoming'])) {
  $incoming_members = $sheets->spreadsheets_values->get($incoming_members_id, $incoming_members_range)->getValues();
  $headers = $incoming_members[0];
  // make sure we don't get problems if somebody changes something
  if(!($headers[1] == 'First' && $headers[2] == 'Last' && $headers[3] == 'Email')) {
    error_log("Incoming Members Spreadsheet changed!");
    http_response_code(500);
    die();
  }
  
  foreach($incoming_members as $i=>$row) {
    if($i == 0 || trim($row[3]) === "") continue;
    $nameFirst = $row[1];
    $nameLast = $row[2];
    $email = $row[3];
    if(!array_key_exists($email, $members)) {
      $members[$email] = array(
        "email" => $email,
        "name-first" => $nameFirst,
        "name-last" => $nameLast
      );
    }
  }
}

http_response_code(200);
header("Content-Type: application/json");
echo json_encode($members);