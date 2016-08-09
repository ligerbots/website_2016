<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');

$url = $_SERVER[ "REQUEST_URI" ];
$postid = url_to_postid( $url );

$post = get_post( $postid );
$isPage = is_page( $post );

?>

<!DOCTYPE html>
<html>
  <?php
     if ( $isPage ) {
         page_head( $post->post_title );
     } else {
         page_head( "LigerBots Blog", true );
     }
  ?>
  
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
               if ( $isPage ) {
                  echo '<div class="row side-margins bottom-margin">';
                  echo apply_filters( 'the_content', $post->post_content );
                  echo "</div>\n";
               } else {
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

                  echo '<br clear="all" /><br>'. "\n";
                  echo '<div class="blog-newer">';
                  previous_post_link();
                  echo '</div>';
                  echo '<div class="blog-older">';
                  next_post_link();
                  echo "</div>\n";

                  echo '<br clear="all"><div class="blog-feed"><a type="application/rss+xml" href="/?feed=rss">';
                  echo '<img src="/images/feed-icon.svg" width="32px">LigerBots Blog Feed';
                  echo "</a></div>\n";
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
