<?php
require_once( "include/page_elements.php" );
require_once( "include/utils.php" );

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
                <center><div class="notindex-title">Team Carpools</div></center>
                <br/>
                <center>
                  Monday at South, Room 9170, 6:30-8:30 PM<br>
                  Thursday at North, Engineering Room, 6:30-8:30 PM<br>
                  <!--
                       Mondays-Thursdays: 6-9 PM<br>
                       Fridays: 3-9 PM<br>
                       Saturdays: 8:30 AM - 2:30 PM<br>
                     -->
                  Scroll down for more carpools
                </center>
                <div class="level4-heading">Student Carpool Permission form can be downloaded
                  <a href="/images/docs/Carpool_Permission_2014.pdf" style="text-decoration:underline;" target="_blank"><b>here</b></a>.<br/>
                  Driver CORI/SORI forms and instructions can be found
                  <a href="http://www.newton.k12.ma.us/Page/2145" style="text-decoration:underline;" target="_blank"><b>here</b></a>.
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
  </body>
</html>
