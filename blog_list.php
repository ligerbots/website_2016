<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');

if ( isset( $_GET['id'] ) ) {
    $offset = intval( $_GET['id'] );
} else {
    $offset = 0;
}

$nPosts = wp_count_posts()->publish;
$nPerPage = 5;
$args = array(
    'posts_per_page' => $nPerPage,
    'offset' => $offset,
    'orderby' => 'date',
    );
$posts = get_posts( $args );

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
               foreach ($posts as $post) 
               {
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
               /*echo apply_filters( 'the_content', $page->post_content );*/

                 echo '<br clear="all" />'. "\n";
               }

               if ( $offset > 0 ) {
                   $newid = max( 0, $offset - $nPerPage );
                   echo '<div class="blog-newer"><a href="/blog.php?id=' . $newid . '">Newer Posts</a></div>';
               }
               $newid = $offset + $nPerPage;
               if ( $newid < $nPosts ) {
                   echo '<div class="blog-older"><a href="/blog.php?id=' . $newid . '">Older Posts</a></div>';
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
