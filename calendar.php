<?php
   require_once( "include/page_elements.php" );
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Calendar" ); ?>

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
              
              <iframe class="calendar"
                      src="https://www.google.com/calendar/embed?src=ligerbots.com_n95omorir7fj2bg2lu5q4ef8q0%40group.calendar.google.com&ctz=America/New_York">
              </iframe>
            </div>

            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
