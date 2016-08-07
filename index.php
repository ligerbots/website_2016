<?php
   require_once( "include/page_elements.php" );
   require_once( "include/utils.php" );
   $blog = get_latest_blog();
   ?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots" ); ?>

  <body>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
           output_header(); 
           output_navbar();
           ?>

        <div class="page-body">
          <div class="row side-margins bottom-margin"> </div>
          <div class="row side-margins">
	    <div class="col-md-6 bottom-margin">
              <div class="orange-border bottom-margin">
	        <div class="orange-title">
	          LIGERBOTS BLOG
	        </div>
                <div class="home-image-box">
                  <?php echo find_first_image( $blog ); ?>
	        </div>
                <div class="side-margins">
                  <?php
                     my_setup_postdata( $blog );
                     the_excerpt();
                     ?>
                  <div class="read-more bottom-margin">
                    <?php echo '<a href="' . get_permalink( $blog ) . '">'; ?><img src="images/read_more.png"/></a>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 bottom-margin">
              <div class="blue-border bottom-margin">
                <div class="blue-title">
                  UPCOMING EVENTS
                </div>
                  <iframe src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;mode=AGENDA&amp;height=500&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=ligerbots.com_n95omorir7fj2bg2lu5q4ef8q0%40group.calendar.google.com&amp;color=%23711616&amp;ctz=America%2FNew_York"
                          width="100%" height="500" frameborder="0" scrolling="no">
                  </iframe>
              </div>
            </div>
          </div>
          
          <div class="row side-margins">
            <div class="col-md-6 bottom-margin">
              <div class="blue-border">
                <div class="blue-title">
                  ANNOUNCEMENTS
                </div>
                <div class="blue-post side-margins" >
                  <?php
                     foreach ( get_announcements( 5 ) as $ann )
                     {
                     my_setup_postdata( $ann ); 
                     echo '<div class="announce"><div class="announce-title">';
                     echo '<a href="' . get_permalink( $ann ) . '">';
                     the_title();
                     echo "</a></div>\n";
                     echo '<div class="announce-date">';
                     the_date();
                     echo "</div>\n";
                     echo '<div class="announce-content">';
                     the_excerpt();
                     echo "</div>\n";
	             echo "</div>\n";
                     }
                     ?>
	        </div>
	      </div>
	    </div>
	    <div class="col-md-6 bottom-margin">
	      <div class="orange-border">
	        <div class="orange-title">
	          TWITTER
	        </div>
                <a class="twitter-timeline" width="100%" href="https://twitter.com/LigerBots" data-widget-id="728971894213447680" data-chrome="noheader nofooter noborders">Tweets by @LigerBots</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	      </div>
	    </div>
          </div>

          <div class="row wide-side-margins">
            <div class="blue-border home-image-box">
              <img src="/images/team_photo_2015.jpg"/>
            </div>
            <div>
              <p class="blue-label">St Louis 2015</p>
            </div>
          </div>

          <?php output_footer( true ); ?>

        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
