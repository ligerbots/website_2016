<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');

$url = $_SERVER[ "REQUEST_URI" ];
$postid = url_to_postid( $url );

$post = get_post( $postid );

?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Blog" ); ?>
  
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
               setup_postdata( $post ); 
               
               echo '<div class="blog-title">';
               the_title();
               echo "</div>\n";
               echo '<div class="blog-date">';
               the_date();
               echo "</div>\n";
               echo '<div class="blog-content">';
               the_content();
               echo "</div>\n";

               echo '<br clear="all" />'. "\n";
               echo '<div class="blog-newer">';
               previous_post_link();
               echo '</div>';
               echo '<div class="blog-older">';
               next_post_link();
               echo '</div>';
             ?>
            
          </div>

          <?php output_footer(); ?>
        
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
