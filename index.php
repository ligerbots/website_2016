<?php
require_once( "include/page_elements.php" );
require_once( "include/utils.php" );

if ( isset( $_GET['page_id'] ) ) {
    # redirect to page php
    $url = "/page.php?page_id=" . $_GET['page_id'];
    if ( isset( $_GET['preview'] ) ) $url .= "&preview=" . $_GET[ 'preview' ];
    header( "Location: $url" );
    die();
}

$blog = get_latest_blog();
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots" ); ?>

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
              <div class="col-md-6 col-sm-12">
                <div class="panel panel-orange">
                  <div class="panel-heading index-heading">
                    <!-- CSS cannot seem to set the color so do it here -->
                    <a style="color:white;" href="/blog_list.php">LIGERBOTS BLOG</a>
                  </div>
                  <div id="blog-panel" class="panel-body">
                    <div class="blog-image-box">
                      <?php echo find_first_image( $blog ); ?>
                    </div>
                    <div class="text-margins">
                      <?php
                      my_setup_postdata( $blog );
                      my_the_excerpt( FALSE );
                      ?>
                      <div class="read-more">
                        <a href="<?php echo get_permalink( $blog ); ?>"><img src="/images/read_more_flat.svg"/></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6 col-sm-12">
                <div class="panel panel-blue">
                  <div class="panel-heading index-heading">
                    <a style="color:white;" href="/calendar.php">UPCOMING EVENTS</a>
                  </div>
                  <div id="cal-panel-div" class="panel-body">
                    <iframe id="cal-panel" src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;mode=AGENDA&amp;height=500&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=ligerbots.com_n95omorir7fj2bg2lu5q4ef8q0%40group.calendar.google.com&amp;color=%23711616&amp;ctz=America%2FNew_York"
                            width="100%" height="500" frameborder="0" scrolling="no">
                    </iframe>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row bottom-margin row-margins">
              <div class="col-md-6 col-sm-12">
                <div class="panel panel-blue">
                  <div class="panel-heading index-heading">
                    <a style="color:white;" href="/blog_list.php">ANNOUNCEMENTS</a>
                  </div>
                  <div id="ann-panel" class="panel-body" >
                    <?php
                    foreach ( get_announcements( 4 ) as $ann )
                    {
                        my_setup_postdata( $ann ); 
                        echo '<div class="announce text-margins"><div class="announce-title">';
                        echo '<a href="' . get_permalink( $ann ) . '">';
                        the_title();
                        echo "</a></div>\n";
                        echo '<div class="announce-date">';
                        the_date();
                        echo "</div>\n";
                        echo '<div class="announce-content">';
                        my_the_excerpt( TRUE );
                        echo "</div>\n";
                        echo "</div>\n";
                    }
                    ?>
                    <br/>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="panel panel-orange">
                  <div class="panel-heading index-heading">
                    <a style="color:white;" target="_blank" href="https://twitter.com/search?q=ligerbots&src=typd">TWITTER</a>
                  </div>
                  <div class="panel-body">
                    <a class="twitter-timeline" width="100%" href="https://twitter.com/LigerBots" data-widget-id="728971894213447680" data-chrome="noheader nofooter noborders">Tweets by @LigerBots</a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                  </div>
                </div>
              </div>
            </div>

            <div class="row row-margins">
              <div class="col-xs-12">
                <div class="panel panel-brag">
                  <img src="/images/team_photo_2018.jpg"/>
                </div>
                <div style="text-align:center;">
                  <div class="label-blue">Detroit World Championship 2018</div>
                </div>
              </div>
            </div>
            
            <?php output_footer(); ?>

          </div>
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
    <script>
     function Resize( id1, id2 )
     {
         $(id2).css( 'height', $(id1).height() );
     }
     
     function FixHeight()
     {
         var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
         if ( width > 990 ) {
             Resize( "#blog-panel", "#cal-panel" );
             Resize( "#ann-panel", "#twitter-widget-0" );
         }
     }

     $(window).on('load resize', FixHeight);
     $("#twitter-widget-0").on('load', FixHeight);
     FixHeight();
    </script>
  </body>
</html>
