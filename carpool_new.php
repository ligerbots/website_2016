<?php

// This will be in a separate file or on the server
// define('REGISTRATION_KEY', 'secret');
// define('API_URL', 'http://example.com/api/signup');
// define('REDIRECT_URL', 'http://example.com/u/');

// $id = 10002; # TODO figure this out
$user = wp_get_current_user();
// // testing
// $user = (object) array(
//     "ID" => 10002,
//     "first_name" => "John",
//     "last_name" => "Doe",
//     "user_email" => "john.doe@example.com",
//     "team_role" => array( "Student" ),
//     "phone" => "123-456-7890"
// );

if ( !$user || $user->ID == 0 ) {
    http_response_code(401);
    die("Unauthorized");
}

$api_url = defined("API_URL") ? constant("API_URL") : "";
$redirect_url = defined("REDIRECT_URL") ? constant("REDIRECT_URL") : "";
$key = defined("REGISTRATION_KEY") ? constant("REGISTRATION_KEY") : "";

$ch = curl_init();

$data = json_encode(array(
    "registration" => array(
        "firstname" => $user->first_name,
        "lastname" => $user->last_name,
        "email_address" => $user->user_email,
        "groups" => $user->team_role,
        "phone_number" => $user->phone,
        // "password" => $password,
        "is_admin" => false,
        "carpool_driver_eligible" => false,
    ),
    "key" => $key,
));

curl_setopt_array($ch, array(
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
));

$res = curl_exec($ch);

if (curl_errno($ch)) {
    // Handle error
    http_response_code(500);
    die("Error: " . curl_error($ch));
}
$json = json_decode($res);

$token = $json->token;

http_response_code(303);
header("Location: " . $redirect_url . $token, true, 303);
?>