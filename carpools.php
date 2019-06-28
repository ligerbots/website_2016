<?php
require_once( "include/page_elements.php" );
require_once( "include/utils.php" );
http_response_code(200); // override wp

// Form submitted

if ( isset( $_POST[ 'addcarpool' ] ) ) {
    $cpId = $_POST[ 'carpoolid' ];
    if ( $cpId ) {
        add_carpool( $cpId );
    }
}

if ( isset( $_POST[ 'delcarpool' ] ) ) {
    $cpId = $_POST[ 'carpoolid' ];
    if ( $cpId ) {
        delete_carpool( $cpId );
    }
}
?>


<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Carpools" ); ?>

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
                <center><div class="notindex-title">TEAM CARPOOLS</div></center>
                <br/>
                <center>
            <!-- Mondays, Wednesdays and Thursdays at South, 6-9 PM<br/>
            Tuesdays at North, 6-9 PM (mostly for Build Leaders)<br/>
            Fridays at South, 3-9 PM<br/>
            Saturdays at South, 9 AM - 2:30 PM<br/> -->
                 Mondays and Thursdays at South, 6:30-9 PM</br>
		  <!-- Mondays at North, Thursdays at South, 6:30-9 PM</br> -->
		  </br>
		  North Meetings in the Engineering Room</br>
		  South Meetings in the Wood Shop, Room 9170</br>
                  Scroll down for more carpools
                </center>
                <div class="level4-heading">Student Carpool Permission form can be downloaded
                  <a href="https://drive.google.com/open?id=0B-X2pQSmn_-zSHNmQUtrU1JMMHM" style="text-decoration:underline;" target="_blank"><b>here</b></a>.<br/>
                  CORI/SORI forms must be submitted <b>in person</b> at a school office: 
                  <a href="https://drive.google.com/open?id=0B8rfh_l5qtZ5Wkx3ZFR6a19OM3c" style="text-decoration:underline;" target="_blank"><b>CORI</b></a>
                  <a href="https://drive.google.com/open?id=0B8rfh_l5qtZ5S3A1ZngwUjVvRkU" style="text-decoration:underline;" target="_blank"><b>SORI</b></a>.
                </div>

                <?php
                // If the user is an editor, give them the option to add and delete carpools
                if ( current_user_can( 'edit_posts' ) ) {
                    echo '<br><form class="form-inline" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                    echo '<div class="form-group">';
                    echo '<label for="carpoolid">Enter ID of carpool to add:</label>';
                    echo '<input type="text" class="form-control" id="carpoolid" name="carpoolid" placeholder="q0xim9">';
                    echo '</div>';
                    echo '<button type="submit" name="addcarpool" class="btn btn-default">Add Carpool</button>';
                    echo "</form>\n";
                }
                
                foreach ( fetch_carpools() as $row ) {
                    if ( current_user_can( 'edit_posts' ) ) {
                        // Delete button
                        echo '<form class="form-inline carpool-delete" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                        echo '<input type="hidden" name="carpoolid" value="' . $row['ID'] . '">';
                        echo '<button type="submit" name="delcarpool" class="btn btn-danger">Delete</button>';
                        echo "</form>\n";
                    }
                    echo '<iframe src="https://www.groupcarpool.com/t/' . $row[ "LABEL" ] . '" class="carpool" id="' . $row[ "ID" ] . '"></iframe>' . "\n";
                }
                ?>
              </div>
            </div>
            
            <?php output_footer(); ?>
          </div>
        </div>
      </div>
    </div>    
    <?php page_foot(); ?>
    <script type="text/javascript">
     var idleTime = 0;
     $(document).ready(function () {
         // Increment the idle time counter every minute
         var idleInterval = setInterval(idleIncrement, 60000); // 1 minute
         
         // Zero the idle timer on mouse movement or key press
         $(this).mousemove(function (e) {
             idleTime = 0;
         });
         $(this).keypress(function (e) {
             idleTime = 0;
         });
     });
     
     function idleIncrement() {
         idleTime = idleTime + 1;
         if ( idleTime >= 10 ) { // minutes
             // refresh the whole page. That way, removed carpools will go away.
             window.location.reload();
         }
     }
    </script>   
  </body>
</html>
