<?php
   require_once( "include/page_elements.php" );
   require_once( "include/utils.php" );
   
   require_once( "attendance-backend/api/functions.php" );
   
   $wp_id = get_current_user_id();
   $attendanceUser;
   if($wp_id > 0) {
       $attendanceUser = new User($wp_id, USER_SELECTOR_UNAME);
       if($attendanceUser->error !== false) {
           $wp_info = wp_get_current_user();
           createUser($wp_id, $wp_info->first_name, $wp_info->last_name, $wp_info->user_email);
           $attendanceUser = new User($wp_id, USER_SELECTOR_UNAME);
       }
   } else {
       header("Location: /");
   }
   
   $attendanceInfo = getUserInfo($wp_id);
   date_default_timezone_set("America/New_York"); 
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Attendance" ); ?>

  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-xs-12 no-side-padding">

        <?php 
           output_header(); 
           output_navbar();
           ?>

        <div class="row page-body">
          <div class="col-md-12 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row side-margins row-margins bottom-margin">
              <h2>Attendance</h2>
              
              <p><b>PIN: </b> <?=$attendanceInfo['pin']?></p>
              <p><b>RFID tag: </b> <?=$attendanceInfo['rfid']==""?"Not set":$attendanceInfo['rfid']?></p>
              
              <p>
                <b>Hours: </b> <?= $hours = floor(floatval($attendanceInfo["time"]) / 3600); ?>/50<br/>
                <progress id="hours-tracker" max="50" value="<?= $hours; ?>"></progress>
              </p>
              
              <p>Meetings attended:</p>
              <ul>
                  <?php 
                    $evts = getUsersEvents($wp_id);
                    foreach($evts as $i=>$evt) {
                        $start = date("d/m/y h:i a", $evt['start']);
                        $hours = ($evt['end'] - $evt['start']) / 3600.0;
                        ?> <li><b><?=$start;?></b>: <?php printf("%.1f", $hours); ?> hours</li> <?php
                    }
                    if(sizeof($evts) == 0) {
                        ?> <li>None</li> <?php
                    }
                  ?>
              </ul>
            </div>

            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
