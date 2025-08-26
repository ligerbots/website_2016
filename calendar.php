<?php
require_once( 'include/page_elements.php' );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once( 'wp-backend/wp-blog-header.php' );
http_response_code(200); // override wp
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
            <div class="row side-margins bottom-margin">
              <div class="col-xs-12">
              
                <?php if ( is_user_logged_in() ): ?>
                   <iframe class="calendar" src="https://calendar.google.com/calendar/embed?src=c_ab7c91eb08574f09684076109ea998ec2df38bb839ff68399f801f6274446d76%40group.calendar.google.com&src=c_3e618392cdc25ab00a8623bc6ccf6be5d134a34cd64b6655d524270a66fe2ff4%40group.calendar.google.com&color=%23711616&ctz=America/New_York"></iframe>
                <?php else: ?>
                   <iframe class="calendar" src="https://calendar.google.com/calendar/embed?src=c_ab7c91eb08574f09684076109ea998ec2df38bb839ff68399f801f6274446d76%40group.calendar.google.com&ctz=America%2FNew_York"></iframe>
                <?php endif ?>
              </div>
            </div>
            
            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
