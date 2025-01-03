<?php
require_once( "include/page_elements.php" );
require_once( "include/utils.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require('wp-backend/wp-blog-header.php');

$url = $_SERVER[ "REQUEST_URI" ];
$postid = url_to_postid( $url );

if($postid == 0) { // does not exist
  require("404.php");
  die();
}

$post = get_post( $postid );
$isPage = is_page( $post );
$includeFooter = ! ( $isPage && ( get_page_uri( $postid ) == "current-sponsors" ||
	       	     	     	  get_page_uri( $postid ) == "test-sponsors" ) );
?>

<!DOCTYPE html>
<html>
  <?php
  if ( $isPage ) {
      // if ( get_page_uri( $postid ) == "sponsor" )
      // {
      //    // HACK
      //    $extraHTML = "<meta property=\"og:image\" content=\"https://ligerbots.org/images/cheering_photo_2018.jpg\"/>\n";
      // 	 $extraHTML .= '<meta property="og:description" content="' . htmlspecialchars("Help send the LigerBots to the Detroit FRC World Championship! The team has only a few days to raise $15k for fees, transportation and lodging. Please share with friends, family, STEM supporters and companies who care about supporting STEM. Learn more and donate: https://ligerbots.org/sponsor") . '"/>';
      //    page_head( "Help Send the LigerBots to Championships", false, NULL, $extraHTML );
      // } else {
      page_head( $post->post_title );
      //      }
  } else {
      setup_postdata( $post );
      $htmlcontent = fetch_the_content();
      preg_match( '/<img[^>]+src=[\'"](?P<src>.+?)[\'"][^>]*>/i', $htmlcontent, $result );
      $imageURL = $result['src'];
      $extraHTML = "<meta property=\"og:image\" content=\"$imageURL\"/>\n";
      $extraHTML .= '<meta property="og:description" content="' . htmlspecialchars(get_the_excerpt($post)) . '"/>';
      page_head( "LigerBots Blog - " . $post->post_title, true, NULL, $extraHTML );
  }
  ?>
  
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
            <div class="row bottom-margin text-background">
              <div class="col-md-10 col-md-offset-1 col-sm-12">
                <?php
                if ( $isPage ) {
                    $title = strtoupper( get_the_title() );
                    echo '<center><div class="notindex-title">' . $title . "</div></center>\n";
                    echo $post->post_content;
                } else { 
                    echo '<div class="level4-heading">';
                    the_title();
                    echo "</div>\n";
                    echo '<div class="announce-date">';
                    the_date();
                    echo "</div>\n";

                    echo '<div class="blog-content">';
                    echo $htmlcontent;
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
            </div>

            <?php if ( $includeFooter ) output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
