<?php
require_once( "include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');
http_response_code(200); // override wp

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
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-xs-12 no-side-padding">

        <?php 
        output_header(); 
        output_navbar();
        ?>

        <div class="row page-body">
          <div class="col-md-12 offset-md-0 col-sm-10 offset-sm-1 col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row bottom-margin text-background">
              <div class="col-md-10 offset-md-1 col-sm-12">

                <?php
                foreach ($posts as $post) 
                {
                    setup_postdata( $post ); 

                    echo '<div class="level4-heading">';
                    the_title();
                    echo "</div>\n";
                    echo '<div class="announce-date">';
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
                    echo '<div class="blog-newer">&laquo; <a href="/blog_list.php?id=' . $newid . '">Newer Posts</a></div>';
                }
                $newid = $offset + $nPerPage;
                if ( $newid < $nPosts ) {
                    echo '<div class="blog-older"><a href="/blog_list.php?id=' . $newid . '">Older Posts</a> &raquo;</div>';
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
