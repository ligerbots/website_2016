<?php
  require_once( "include/page_elements.php" );
  require_once( "include/utils.php" );
  http_response_code(200); // no wordpress, this isn't an error
  
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
      
      .attendance-info td {
        padding: 0 0.5em;
      }
      
      .attendance-meetings td, .attendance-meetings th {
        padding: 0 1em;
      }
      
      .attendance-meetings, .attendance-meetings tr, .attendance-meetings td, .attendance-meetings th {
        border: 1px solid rgb(208,78,29);
        border-collapse: collapse;
      }
      
      .attendance-info, .attendance-meetings {
        margin-left: auto;
        margin-right: auto;
      }
      
      .notindex-title {
        margin-bottom: 3rem;
      }
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
              <center><div class="notindex-title">MY ATTENDANCE</div></center>
              
              <?php 
                $evts = getUsersEvents($attendanceUser);
              ?>
              
              <table class="attendance-info">
                <tr>
                  <th>PIN:</th>
                  <td><?=$attendanceInfo['pin']?></td>
                </tr>
                <tr>
                  <th>RFID Tag:</th>
                  <td><?=$attendanceInfo['rfid']==""?"Not set":$attendanceInfo['rfid']?></td>
                </tr>
                <tr>
                  <th>Total Hours:</th>
                  <td><?= floor(floatval($attendanceInfo["time"]) / 3600); ?></td>
                </tr>
                <tr>
                  <th>Total Heetings:</th>
                  <td><?= sizeof($evts); ?></td>
                </tr>
              </table>
              
              <?php
                if(current_user_can('edit_posts')) {
              ?>
                <div class="level4-heading"><a name="meeting"></a>Admin</div>
                <center>
                  <a href="/attendance-backend/api/table.php">Export All Attendance Data</a>
                </center>
              <?php  
                }
              ?>
              
              <div class="level4-heading"><a name="meeting"></a>Meetings attended</div>
              <table class="attendance-meetings">
                  <?php
                    if(sizeof($evts) == 0) {
                      ?> <tr><td>No meetings</td></tr> <?php
                    } else {
                  ?>
                  <tr>
                    <th>Date/Time</th>
                    <th>Hours</th>
                    <th>Additional info</th>
                  </tr>
                  <?php
                    }
                    
                    foreach($evts as $i=>$evt) {
                        $elClass = "";
                        if($evt['meta'] & CALENDAR_MODIFIED || $evt['meta'] & CALENDAR_GIVEN) {
                          $elClass = "modified";
                        }
                        if($evt['meta'] & CALENDAR_SUSPENDED) {
                          $elClass = "suspended";
                        }
                        
                        ?>
                        <tr class="<?=$elClass;?>">
                        <?php
                        if($evt['isopen']) {
                          $start = date("m/d/y h:i a", $evt['start']);
                          ?> <td><?=$start;?></td> <td>ongoing</td> <?php
                        } else {
                          $start = date("m/d/y h:i a", $evt['start']);
                          $hours = ($evt['end'] - $evt['start']) / 3600.0;
                          ?> <td><?=$start;?></td> <td><?php printf("%.1f", $hours); ?></td> <?php
                        }
                        ?> <td> <?php
                        if($evt['meta'] & CALENDAR_MODIFIED || $evt['meta'] & CALENDAR_GIVEN) {
                          ?> (modified)<?php
                        }
                        if($evt['meta'] & CALENDAR_SUSPENDED) {
                          ?> (suspended)<?php
                        }
                        ?>
                          </td>
                        </tr> <?php
                    }
                  ?>
              </table>
            </div>

            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
