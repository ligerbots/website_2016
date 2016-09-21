<?php
  require_once( "include/page_elements.php" );
  require_once( "include/utils.php" );
  
  require_once( "attendance-backend/api/functions.php" );
  
  $wp_id = get_current_user_id();
  $attendanceUser;
  if($wp_id > 0) {
    try {
      $attendanceUser = new User($wp_id, USER_SELECTOR_ID);
    } catch(Exception $e) {
      // uh oh
      error_log("Exception: " . $e->getMessage());
      die("Oh no, there was a database error :(");
    }
  } else {
    header("Location: /");
  }
  
  $attendanceInfo = getUserInfo($attendanceUser);
  date_default_timezone_set("America/New_York"); 
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Attendance" ); ?>

  <body>
    <style>
      .suspended { color: red; }
      .modified { color: blue; }
    </style>
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
                    $evts = getUsersEvents($attendanceUser);
                    foreach($evts as $i=>$evt) {
                        $elClass = "";
                        if($evt['meta'] & CALENDAR_MODIFIED || $evt['meta'] & CALENDAR_GIVEN) {
                          $elClass = "modified";
                        }
                        if($evt['meta'] & CALENDAR_SUSPENDED) {
                          $elClass = "suspended";
                        }
                        
                        ?> <li class="<?=$elClass;?>"> <?php
                        if($evt['isopen']) {
                          $start = date("d/m/y h:i a", $evt['start']);
                          ?><b><?=$start;?></b>: ongoing<?php
                        } else {
                          $start = date("d/m/y h:i a", $evt['start']);
                          $hours = ($evt['end'] - $evt['start']) / 3600.0;
                          ?><b><?=$start;?></b>: <?php printf("%.1f", $hours); ?> hour(s)<?php
                        }
                        if($evt['meta'] & CALENDAR_MODIFIED || $evt['meta'] & CALENDAR_GIVEN) {
                          ?> (modified)<?php
                        }
                        if($evt['meta'] & CALENDAR_SUSPENDED) {
                          ?> (suspended)<?php
                        }
                        ?> </li> <?php
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
