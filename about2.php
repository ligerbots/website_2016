<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');

$page = get_page_by_title( 'About' );

?>

<!DOCTYPE html>
<html>
  <?php page_head( "About the LigerBots" ); ?>
  
  <body>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
           output_header(); 
           output_navbar();
           ?>

        <div class="page-body">
          <div class="row side-margins bottom-margin">
            <?php 
               echo apply_filters( 'the_content', $page->post_content ); 
            ?>
          </div>

          <?php output_footer(); ?>
        
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
