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
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Directory" ); ?>

  <body>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
           output_header(); 
           output_navbar();
           ?>
        
        <div class="page-body">
          <div class="row side-margins bottom-margin">

            <center><h1>LigerBots Directory</h1>
              The information on this page is confidential - It is only available to registered and approved users.
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
               $args = array(
	          'orderby'      => 'login',
	          'order'        => 'ASC', ); 
               $userlist = get_users( $args ); 
               foreach ( $userlist as $user )
               {
                 echo '<tr>';
                 echo '  <td>' . esc_html( $user->first_name ) .'</td>';
                 echo '  <td>' . esc_html( $user->last_name ) .'</td>';
                 echo '  <td>' . esc_html( $user->get( 'phone' ) ) . '</td>';
                 echo '  <td>' . esc_html( $user->user_email ) .'</td>';

                 $addr = join( ', ', array( $user->get( 'address' ), $user->get( 'city' ), 
                                            join( ' ', array( $user->get( 'state' ), $user->get( 'postalcode' ) ) ) ) );
                 if ( $addr == ', ,  ' ) $addr = '';
                 echo '  <td>' . esc_html( $addr ) . '</td>';
                 echo '  <td>' . esc_html( $user->get( 'school' ) ) . '</td>';
                 echo '  <td>' . esc_html( join( ', ', $user->get( 'team_role' ) ) ) . '</td>';
                 echo '</tr>';
               }
                ?>
              </tbody>
            </table>
          </div>

          <?php output_footer(); ?>
        
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
