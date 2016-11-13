<!DOCTYPE html>
<html>
	<?php page_head( "LigerBots Gallery", false, "/css/gallery.css" ); ?>
  <body>
	<?php include '/include/gallery.php' ?>
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
            <div class="row side-margins row-margins bottom-margin">
                <center><div class="notindex-title" style="margin-bottom: 3rem;">GALLERY</div></center>
                <p><center>
                    LigerBots Flickr: <a href="https://www.flickr.com/photos/ligerbots/" target="_blank">https://www.flickr.com/photos/ligerbots/</a><br/>
                    LigerBots Videos: <a href="https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA" target="_blank">https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>
                </center></p>
                <div id="gallery" style="z-index: 1;">
					<?php
						foreach ($photoYears as $year) {
                    		echo "<div id=\"gallery-$year\" style=\"z-index: 2;\">";
                        	foreach ($photoAlbums[$year] as $album) {
								echo "<div id=\"gallery-$year-$album\" style=\"z-index: 3;\">";
                            		echo "<img src=$mainPhoto[$album] style=\"z-index: 4;\">";
                            		echo "<div id=\"gallery-$year-$album-caption\" style=\"psition: absolute; bottom: 0; z-index: 4;\">"
											echo "<p style=\"align: center;\">$caption[$album]</p>"
                           			</div>
                        		</div>
                    		</div>
                </div>

            </div>

            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
    <script type="text/javascript" src="/js/unveil/jquery.unveil.js"></script>
    <script type="text/javascript" src="/js/gallery.js"></script>
  </body>
</html>
