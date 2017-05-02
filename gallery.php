<?php
require_once( "include/page_elements.php" );
http_response_code(200); // override wp

require_once("include/gallery-utils.php");
$flickr = createFlickr();

// a list of urls for each photo in the album
if ( isset( $_GET[ "album" ] ) )
    $albumPhotos = getPhotoList( $flickr, $_GET["album"] );
else
{
    if ( isset( $_GET[ "year" ] ) )
        $year = $_GET[ "year" ];
    else
        $year = 0;

    $albumList = getAlbums( $flickr );
}
?>


<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Gallery", false,
                   array( "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css",
                          "/css/gallery.css" ) ); ?>

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
            <div class="row side-margins bottom-margin">
              <div class="col-xs-12">
		<div class="title-bar">
		  <div class="notindex-title">
		    <a href="/" class="breadcrumbs-link">
		      <img src="/images/home_icon.svg" class="breadcrumbs-link breadcrumbs-home" style="filter: drop-shadow(1.5pt 1.5pt 1pt rgba(0,0,0,0.25))"> <!-- svg takes a different shadow format than text !-->
		    </a>
		    <span class="glyphicon glyphicon-chevron-left breadcrumbs-chevron-1"></span>
		    <span class="glyphicon glyphicon-chevron-left breadcrumbs-chevron-2"></span>
		    <?php
		    if ( isset( $albumPhotos ) ) //currently viewing photos in an album
		    {
			echo "<a href=\"gallery.php?year=" . $albumPhotos['yearIndex'] . "\" class=\"breadcrumbs-link\">\n";
			echo strtoupper( $albumPhotos[ "yearTitle" ] ) . "\n";
			echo "</a> \n";
			echo "<span class=\"glyphicon glyphicon-chevron-left breadcrumbs-chevron-1\"></span>\n";
			echo "<span class=\"glyphicon glyphicon-chevron-left breadcrumbs-chevron-2\"></span>\n";
			echo strtoupper( $albumPhotos[ "title" ] ) . "\n";
		    } else { // viewing a year's album list
			echo "PHOTOS\n";
		    }
		    ?>
		  </div>
		</div>
                
		<?php
                // if in the album list, show the links to Flickr and youTube
		if ( !isset( $albumPhotos ) ) 
		{
		    echo "<center style=\"margin-bottom: 3em; word-wrap: break-word;\">"; //add proper margins & allow the youtube url to be wrapped on mobile
		    echo "<h5 class=\"gallery-link\">To see all LigerBots photos:";
		    echo "<a href=\"https://www.flickr.com/photos/ligerbots/\">flickr.com/photos/ligerbots/</a>";
		    echo "</h5>";
		    echo "<h5 class=\"gallery-link\">To see all LigerBots videos:";
		    echo "<a href=\"https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA\">youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>";
		    echo "</h5>";
		    echo "</center>";
		}
		?>

		<div class="gallery-container">
                <?php
                //the url has no album display specification; show the year view
                if ( ! isset( $_GET[ "album" ] ) )
                {
                    albumListDisplay( $albumList, $year );
                }
                else
                {
                    albumDisplay( $albumPhotos );
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
      <!-- add fancybox image zooming scripts -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
      <script type="text/javascript">
       $.fancybox.defaults.slideShow = false;
       $.fancybox.defaults.fullScreen = false;
       
       function sizeImage( image ) {
	   var aspectRatio = image.naturalHeight / image.naturalWidth;
	   if (aspectRatio <= 1)
	   {
	       $(image).addClass('gallery-photo-wide');
	       $(image).closest('div').addClass('gallery-photo-wide');
	       $(image).closest('div').removeClass('gallery-photo-loading');
	   } else {
	       $(image).addClass('gallery-photo-tall');
	       $(image).closest('div').addClass('gallery-photo-tall');
	       $(image).closest('div').removeClass('gallery-photo-loading');
	   }
       }
      </script>
  </body>
</html>
