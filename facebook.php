<?php
require_once( "include/page_elements.php" );
require_once( "include/utils.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

// must be logged in
if ( ! is_user_logged_in() )
{
    header('Location: /login.php');
    die();
}

if ( isset($_FILES['upload'] ) )
{
    $message = facebookUpload( $_FILES['upload'] );
}

// Comparison function
function acct_type( $user ) {
    $r = $user->get( 'team_role' );
    // careful of the comparison operator!
    if ( array_search( 'Student', $r ) !== FALSE  ) return 0;
    if ( array_search( 'Coach', $r ) !== FALSE || array_search( 'Mentor', $r ) !== FALSE ) return 1;
    return 2;
}

function user_cmp( $a, $b ) {
    $aVal = acct_type( $a );
    $bVal = acct_type( $b );
    if ( $aVal != $bVal )
        return ( $aVal < $bVal) ? -1 : 1;
    
    $aVal = $a->last_name;
    $bVal = $b->last_name;
    if ( $aVal != $bVal )
        return ( $aVal < $bVal) ? -1 : 1;
    
    $aVal = $a->first_name;
    $bVal = $b->first_name;
    if ( $aVal != $bVal )
        return ( $aVal < $bVal) ? -1 : 1;
    return 0;
}
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Facebook" ); ?>
  
  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">
        
        <?php 
        output_header(); 
        output_navbar();
        ?>
        
        <div class="page-body">
          <div class="row side-margins bottom-margin">

            <?php 
            if ( ! empty( $message ) ) echo '<div class="alert">' . $message . '</div>' . "\n";
            ?>
            
            <center><h1>LigerBots Facebook</h1>
              The information on this page is confidential - It is only available to registered and approved users.
            </center>
            
            <?php
            if ( current_user_can( 'edit_posts' ) ) {
                echo '<br><form class="form-inline" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
                echo '<div class="form-group">';
                echo '<label for="upload">Select multiple pictures to upload:</label>';
                echo '<input class="form-control" type="file" id="upload" name="upload[]" multiple>';
                echo "</div>\n";
                echo '<button name="submit" type="submit" class="btn btn-default">Submit</button>';
                echo "</form>\n";
            }

            $userlist = get_users();
            uasort( $userlist, 'user_cmp' );
            $prevType = -1;
            foreach ( $userlist as $user ) {
                $type = acct_type( $user );
                if ( $type != $prevType ) {
                    if ( $prevType >= 0 ) echo '<div style="clear:left"></div>' . "\n";
                    if ( $type == 0 )
                        echo "<h2>Students</h2>\n";
                    else if ( $type == 1 )
                        echo "<h2>Coaches and Mentors</h2>\n";
                    else
                        echo "<h2>Parents</h2>\n";
                }

                echo('<div class="facebook-entry"><span style="text-align: center">');
                echo('<img src="/images/facebook/' . $user->get( 'facebook_image' ) . '" width="150px" height="225px" style="border:1px solid black">');
                echo('</span><br>');
                
                echo '<div class="name';
                if ( array_search( 'Exec', $user->get( 'team_role' ) ) !== FALSE ) echo ' exec';
                echo '">' . $user->first_name . ' ' . $user->last_name . "</div>\n";
                
                if ( $type == 0 ) {
                    // Student: add school name
                    if ( $user->get( 'School' ) == 'North' )
                        echo( '<div class="north">North</div>' );
                    else
                        echo( '<div class="south">South</div>' );
                }
                echo('</div>');
                
                $prevType = $type;
            }
            ?>
          </div>

          <?php output_footer(); ?>
        
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>