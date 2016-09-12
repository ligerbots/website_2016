<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

// Form submitted

$referrer = $_SERVER[ 'HTTP_REFERER' ];

if ( isset( $_GET['logout'] ) ) {
    wp_logout();
    header( "Location: $referrer" );
    exit();
}

if ( isset( $_POST[ 'login' ] ) ) {
    $username = $_POST[ 'username' ];
    $parm = array(
        'user_login' => $username,
        'user_password' => $_POST[ 'password' ],
    );
    if ( $_POST[ 'rememberme' ] == 'on' ) $parm[ 'remember' ] = true;
    $res = wp_signon( $parm, false );
    if ( is_wp_error( $res ) )
    {
        $message = "ERROR: The password you entered for the username '$username' is incorrect.";
    }
    else
    {
        $dest = $_POST[ 'redirect_to' ];
        if ( empty( $dest ) ) $dest = '/';
        header( "Location: $dest" );
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
  <?php page_head( "Login" ); ?>

  <body>
    <div id="header-ghost" ></div>
    <div class="container no-side-padding">
      <div class="col-xs-12 no-side-padding">

        <?php 
           output_header(); 
           output_navbar();
           ?>

        <div class="page-body">
          <div class="row side-margins bottom-margin"> </div>
          <div class="row side-margins bottom-margin">
            <div class="col-xs-4 col-xs-offset-4">
            
              <?php
              if ( ! empty( $message ) )
              {
                  echo '<div class="alert alert-danger"><strong>' . $message . '</strong></div>' . "\n";
              }
              ?>

              <form role="form" action="/login.php" method="post">
                <div class="form-group">
                  <label for="username">Username:</label>
                  <input type="text" class="form-control" name="username" id="username" value="" />
                </div>
                <div class="form-group">
                  <label for="password">Password:</label>
                  <input type="password" name="password" id="password" class="form-control" value="" />
                </div>
                <div class="checkbox">
                  <label><input name="rememberme" type="checkbox" id="rememberme" value="on" />Remember me</label>
                </div>
                <input type="submit" name="login" id="login" class="btn btn-default" value="Log In" />
                <input type="hidden" name="redirect_to" value="<?php echo $referrer; ?>" />
              </form>

              <br/>
              Team members may register <a href="/register.php">here.</a>
            </div>
          </div>
          
          <?php output_footer(); ?>
          
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
