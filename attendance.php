<?php
  require_once( "include/page_elements.php" );
  require_once( "include/utils.php" );
  http_response_code(200); // override wp
  
  date_default_timezone_set("America/New_York");
  
  require_once( "attendance-backend/api/functions.php" );
  
  if(isset($_REQUEST['action'])) {
    if(!current_user_can("edit_posts")) {
      http_response_code(403);
      die("Access denied :(");
    }
    
    if($_REQUEST['action'] == "update") {
      $eventId = intval($_REQUEST['eventId']);
      $startTs = strtotime($_REQUEST['start']);
      $endTs = strtotime($_REQUEST['end']);
      $suspended = $_REQUEST['suspended'] === "true";
      
      updateEvent($eventId, $startTs, $endTs, $suspended, true, false);
      
      header("Content-type: application/json");
      echo json_encode(array("status"=>"success"));
    } else if($_REQUEST['action'] == "create") {
      $startTs = strtotime($_REQUEST['start']);
      $endTs = strtotime($_REQUEST['end']);
      $user = intval($_REQUEST['user']);
      
      createEvent($user, $startTs, $endTs);
      
      header("Content-type: application/json");
      echo json_encode(array("status"=>"success"));
    }
    
    die();
  }
  
  $wp_id = get_current_user_id();
  $attendanceUser;
  $viewing_other = false;
  if($wp_id > 0) {
    if(isset($_GET['view_for']) && trim($_GET['view_for']) == "") {
      unset($_GET['view_for']);
    }
    if(current_user_can('edit_posts') && isset($_GET['view_for'])) {
      $args= array(
        'search' => $_GET['view_for'],
        'search_fields' => array('user_login','user_nicename','display_name')
      );
      $user = new WP_User_Query($args);
      foreach ( $user ->results as $user ) {
		    $wp_id = $user->id;
        $viewing_other = $user;
      }
    }
    
    try {
      $attendanceUser = new User($wp_id, USER_SELECTOR_ID);
    } catch(Exception $e) {
      // uh oh
      error_log("Exception: " . $e->getMessage());
      die("Oh no, there was a database error :(");
    }
  } else {
    header("Location: /login.php?r=%2fattendance.php");
  }
  
  $attendanceInfo = getUserInfo($attendanceUser);
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
                if(current_user_can('edit_posts')) {
              ?>
                <div class="level4-heading" style="margin-top:0">Management</div>
                <center>
                  <a href="/attendance-backend/api/table.php">Export All Attendance Data</a>
                  <form role="form" action="attendance.php" method="get" style="width:300px;margin-top:1em;">
                    <div class="input-group">
                      <input type="text" class="form-control" name="view_for" id="view_for" value="<?=isset($_GET['view_for'])?$_GET['view_for']:"";?>" placeholder="View data for a member..." />
                      <span class="input-group-btn">
                        <input type="submit" class="btn btn-default" value="View" />
                      </span>
                    </div>
                  </form>
                </center>
              <?php  
                }
                
                if(current_user_can('edit_posts') && isset($_GET['view_for']) && !$viewing_other) {
              ?>
              <p>
                <center>User <?=$_GET['view_for'];?> not found</center>
              </p>
              <?php
                } else {
              ?>
              
              <?php 
                $evts = getUsersEvents($attendanceUser);
              ?>
              
              <?php
                if(current_user_can('edit_posts')) {
              ?>
              <div class="level4-heading">
              <?php
                if($viewing_other) {
                  echo "Info for " . $viewing_other->display_name;
                } else {
                  echo "My info";
                }
              ?>
              </div>
              <?php
                }
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
                  <td><?= floor(floatval($attendanceInfo["time"]) / 360) / 10; ?></td>
                </tr>
                <tr>
                  <th>Total Meetings:</th>
                  <td><?php
                    $allDates = array();
                    foreach($evts as $evt) {
                      $eventTs = $evt['start'];
                      $day = date("m/d/y", $eventTs);
                      if(!in_array($day, $allDates)) {
                        $allDates[] = $day;
                      }
                    }
                    echo sizeof($allDates);
                  ?></td>
                </tr>
              </table>
              
              <div class="level4-heading"><a name="meeting"></a>Meetings attended</div>
              <table class="attendance-meetings">
                  <?php
                    if(sizeof($evts) == 0) {
                      ?> <tr><td>No meetings</td></tr> <?php
                    } else {
                  ?>
                  <tr>
                    <th>Date/Time</th>
                    <th><?=current_user_can("edit_posts")?"End date/time":"Hours";?></th>
                    <th>Additional info</th>
                  </tr>
                  <?php
                    }
                    
                    if(current_user_can('edit_posts')) {
                      foreach($evts as $i=>$evt) {
                        ?>
                        <tr>
                        <?php
                        if($evt['isopen']) {
                        ?>
                          <td><input type="datetime-local" step="1" class="event-start" value="<?=date("Y-m-d\TH:i:s", $evt['start']);?>" /></td>
                          <td>ongoing
                            <input type="hidden" class="event-end" value="0" />
                          </td>
                        <?php
                        } else {
                          $hours = ($evt['end'] - $evt['start']) / 3600.0;
                        ?>
                          <td><input type="datetime-local" step="1" class="event-start" value="<?=date("Y-m-d\TH:i:s", $evt['start']);?>" /></td>
                          <td><input type="datetime-local" step="1" class="event-end" value="<?=date("Y-m-d\TH:i:s", $evt['end']);?>" /></td>
                        <?php
                        }
                        ?>
                          <td>
                        <?php
                        if($evt['meta'] & CALENDAR_MODIFIED || $evt['meta'] & CALENDAR_GIVEN) {
                        ?>
                            modified
                            <br/>
                        <?php
                        }
                        ?>
                            <label>
                              <input type="checkbox" class="event-suspended" <?php if($evt['meta'] & CALENDAR_SUSPENDED) echo "checked";?> />
                              suspend
                            </label>
                          </td>
                          <td><button type="button" class="event-save" data-event-id="<?=$evt['id'];?>">Save changes</button></td>
                        </tr>
                        <?php
                      }
                    } else {
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
                        </tr>
                        <?php
                      }
                    }
                  ?>
              </table>
              
              <?php
                  if(current_user_can('edit_posts')) {
              ?>
              <p><center>
              Add event: 
              <input type="datetime-local" step="1" id="add-event-start" />
              <input type="datetime-local" step="1" id="add-event-end" />
              <input type="hidden" id="add-event-user" value="<?=$wp_id;?>" />
              <button type="button" id="add-event-save">Add</button>
              </center></p>
              <?php
                  }
                }
              ?>
            </div>

            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
    
    <script type="text/javascript">
      $(".event-save").click(function() {
        var parent = $(this).parent().parent();
        var eventId = $(this).data("event-id");
        var start = parent.find(".event-start").val();
        var end = parent.find(".event-end").val();
        var suspended = parent.find(".event-suspended").prop("checked");
        console.log(eventId, start, end, suspended);
        
        var self = $(this);
        self.text("Saving...").prop("disabled", true);
        
        $.post("/attendance.php", {
          action: "update",
          eventId,
          start,
          end,
          suspended
        }, function(data) {
          console.log(data);
          self.text("Save changes").prop("disabled", false);
        }).fail(function(err) {
          console.error(err);
          self.text("Error").prop("disabled", false);
        });
      });
      
      $("#add-event-save").click(function(){
        var start = $("#add-event-start").val();
        var end = $("#add-event-end").val();
        var user = $("#add-event-user").val();
        console.log(start, end, user);
        
        var self = $(this);
        self.text("Adding...").prop("disabled", true);
        
        $.post("/attendance.php", {
          action: "create",
          user,
          start,
          end
        }, function(data) {
          console.log(data);
          self.text("Add").prop("disabled", false);
          location.reload();
        }).fail(function(err) {
          console.error(err);
          self.text("Error").prop("disabled", false);
        });
      });
    </script>
  </body>
</html>
