<?php
   require_once( "include/page_elements.php" );
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
	          LIGERBUILD BLOG
	        </div>
                <div class="home-image-box">
	          <img src="https://farm2.staticflickr.com/1474/24349123635_8b23a92b04_c.jpg"/>
	        </div>
	        <p>Work has already begun on the chassis for the 2016 Stronghold robot. Five working groups are preparing the team to hit the ground
	          running on January 9. We have students working on mechanical, electrical, programming and several fixing the road kill. . . .</p>
	        <div class="read-more bottom-margin"><a href="#"><img src="images/read_more.png"/></a></div>
              </div>
	    </div>
	    <div class="col-md-6 bottom-margin">
              <div class="blue-border bottom-margin">
	        <div class="blue-title">
	          LIGERBUSINESS BLOG
	        </div>
                <div class="home-image-box">
                  <img src="https://farm1.staticflickr.com/673/23583263610_7c7a9fdb20_z.jpg"/> 
                </div>
	        <p>FRC alumnus Ian from Dash Electric visited with his awesome electric longboard in December and told us how he started his company. Students got to ride his 
	          board and ask him lots of questions.</p>
	        <div class="read-more bottom-margin"><a href="#"><img src="images/read_more.png"/></a></div>
              </div>
	    </div>
          </div>
          <div class="row side-margins">
	    <div class="col-md-6 bottom-margin">
	      <div class="blue-border">
	        <div class="blue-title">
	          TWITTER
	        </div>
                <a class="twitter-timeline" width="100%" href="https://twitter.com/LigerBots" data-widget-id="728971894213447680" data-chrome="noheader nofooter noborders">Tweets by @LigerBots</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	        <!-- <div id="post1" > -->
	        <!--   <div class="event"> -->
		<!--     <span class="eventitle">Team Dinners Needed During Build<br/> -->
		<!--       Every Friday During January</span> -->
		<!--     <p>Please sign up to make team dinners during build season. We eat together as a team on Friday nights at 6:00. Several families can do this together. Please see the  -->
		<!--       signup <a href="file:///C:/Users/guymi_000/Desktop/Frontpage/test.html">here.</a> -->
	        <!--   </div> -->
	        <!--   <div class="event"> -->
		<!--     <span class="eventitle">Carpool Drivers Needed</span> -->
		<!--     <p>Please sign up to drive carpools from North to South and back, Mondays  -->
		<!--       through Saturdays during build season. Please see details on our carpool page, <a href="http://ligerbots.org">here.</a></p> -->
	        <!--   </div> -->
	        <!--   <div class="event"> -->
		<!--     <span class="eventitle">STIMMS Signup Required</span> -->
		<!--     <p>All Students must sign up on STIMMS or theey will not be able to attend competitions. Please  -->
		<!--       see details <a href="http://ligerbots.org">here.</a></p> -->
	        <!--   </div> -->
	        <!--   <div class="event"> -->
		<!--     <span class="eventitle">No Team Meeting 12/24</span> -->
		<!--     <p>There will be no team meeting 12/24 due to Christmas. Team meetings will resume on 1/4/16.</p> -->
	        <!--   </div> -->
	        <!-- </div> -->
	      </div>
	    </div>
	    <div class="col-md-6 bottom-margin">
	      <div class="orange-border">
	        <div class="orange-title">
	          UPCOMING EVENTS
	        </div>
	        <div id="post2">
                  <iframe src="https://calendar.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;mode=AGENDA&amp;height=500&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=ligerbots.com_n95omorir7fj2bg2lu5q4ef8q0%40group.calendar.google.com&amp;color=%23711616&amp;ctz=America%2FNew_York" 
                          style="border-width:0" width="100%" height="500" frameborder="0" scrolling="no">
                  </iframe>
	        </div>
	      </div>
	    </div>
          </div>

          <div class="row side-margins">
	    <div class="col-md-10 col-md-offset-1">
              <img class="blue-border" src="/images/team_photo_2015.jpg"/>
              <div>
                <p class="blue-label">St Louis 2015</p>
              </div>
            </div>
          </div>

          <?php output_footer(); ?>

        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
