<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

// must be logged in
if ( ! is_user_logged_in() )
{
    header('Location: /login.php');
    die();
}

// Comparison function
function user_cmp($a, $b) {
    $a1 = $a->last_name;
    $b1 = $b->last_name;
    if ( $a1 != $b1 )
        return ( $a1 < $b1) ? -1 : 1;

    $a1 = $a->first_name;
    $b1 = $b->first_name;
    if ( $a1 != $b1 )
        return ( $a1 < $b1) ? -1 : 1;
    return 0;
}
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Directory" ); ?>

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

            <div class="row bottom-margin row-margins">
              <div class="col-xs-12">

                <center>
                  <div class="notindex-title">LigerBots Directory</div>
                  <br/><br/>
                  The information on this page is confidential - It is only available to registered and approved users.
                  <br/>
                </center>

                <table class="table table-condensed table-striped">
                  <thead>
                    <tr>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Phone Number</th>
                      <th>Email</th>
                      <th>Address</th>
                      <th>School</th>
                      <th>Role</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    <?php 
                    $userlist = get_users();
                    // Sort it.
                    uasort( $userlist, 'user_cmp' );
                    foreach ( $userlist as $user )
                    {
                        // Don't list users who have not been approved
                        if ( ! $user->get( 'wp-approve-user' ) ) continue;
                        
                        echo '<tr>';
                        echo '  <td>' . esc_html( $user->first_name ) .'</td>';
                        echo '  <td>' . esc_html( $user->last_name ) .'</td>';
                        echo '  <td>' . esc_html( $user->get( 'phone' ) ) . '</td>';
                        echo '  <td>' . esc_html( $user->user_email ) .'</td>';
                        
                        $addr = join( ', ', array( $user->get( 'address' ), $user->get( 'city' ), 
                                                   join( ' ', array( $user->get( 'state' ), $user->get( 'postalcode' ) ) ) ) );
                        if ( $addr == ', ,  ' ) $addr = '';
                        echo '  <td>' . esc_html( $addr ) . '</td>';
                        $school =  $user->get( 'school' );
                        if ( strtoupper($school) == 'NONE' ) $school = '';
                        echo '  <td>' . esc_html( $school ) . '</td>';
                        echo '  <td>' . esc_html( join( ', ', $user->get( 'team_role' ) ) ) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                  </tbody>
                </table>
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
