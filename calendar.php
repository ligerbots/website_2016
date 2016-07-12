<?php
   require_once( "include/page_elements.php" );
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots" ); ?>

  <body>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
           output_header(); 
           output_navbar();
           ?>

        <div class="page-body">
          <div class="row side-margins bottom-margin"> </div>
          <div class="row side-margins">
            
            <iframe class="calendar bottom-margin"
                    src="https://www.google.com/calendar/embed?src=ligerbots.com_n95omorir7fj2bg2lu5q4ef8q0%40group.calendar.google.com&ctz=America/New_York">
            </iframe>
          </div>

          <?php output_footer(); ?>
        
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
