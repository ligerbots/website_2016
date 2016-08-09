<?php
   require_once( "include/page_elements.php" );

   /* Short and sweet */
   define('WP_USE_THEMES', false);
   require_once('wp-backend/wp-blog-header.php');
?>

<!DOCTYPE html>
<html>
  <?php page_head( "Login" ); ?>

  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
           output_header(); 
           output_navbar();
           ?>

        <div class="page-body">
          <div class="row side-margins bottom-margin"> </div>
          <div class="row side-margins bottom-margin">
            <div class="col-xs-4 col-xs-offset-4">
            
              <form role="form" action="/wp-backend/wp-login.php" method="post">
                <div class="form-group">
                  <label for="email">Email address:</label>
                  <input type="text" class="form-control" name="log" id="user_login" value="" />
                </div>
                <div class="form-group">
                  <label for="pwd">Password:</label>
                  <input type="password" name="pwd" id="user_pass" class="form-control" value="" />
                </div>
                <div class="checkbox">
                  <label><input name="rememberme" type="checkbox" id="rememberme" value="forever" />Remember me</label>
                </div>
                <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-default" value="Log In" />
                <input type="hidden" name="redirect_to" value="/" />
              </form>
            </div>
          </div>
          
          <?php output_footer(); ?>
          
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
