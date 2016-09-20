<?php
include("../../wp-backend/wp-config.php");
//Include the API
include("include/api.php");

//Required permission
setAccess("users.list");

$allWpUsers = get_users();

$users_arr = array();

foreach($allWpUsers as $i=>$wpUser) {
  $user = new User($wpUser->id, USER_SELECTOR_ID);
  $users_arr[] = array(
    id => $user->udata->id,
    fname => $user->udata->fname,
    lname => $user->udata->lname,
    email => $user->udata->email,
    rfid => $user->udata->rfid,
    pin => $user->udata->pin,
    username => $user->udata->username,
    permissions => $user->udata->permissions,
    signedin => $user->udata->signedin
  );
}

//Encode and return result
success($users);
?>