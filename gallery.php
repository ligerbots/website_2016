<?php
require_once( "include/page_elements.php" );
require_once( "include/gallery-utils.php" );
http_response_code(200); // overide WordPress; this page *does* exist

$flickr = createFlickr();

if ( isset($_GET["album"]) )
{
    // viewing an album; get its photos
    $albumPhotos = getPhotoList( $flickr, $_GET["album"] );
    $subtitle = $albumPhotos["albums"][ $albumPhotos["albumIndex"] ]["title"];
}
else
{
    $year = 0;
    if ( isset($_GET["year"]) ) $year = $_GET[ "year" ];
    $albumList = getAlbums( $flickr );
    $subtitle = $albumList[$year]["title"];
}
?>

<!DOCTYPE html>
<html>
  <?php
  page_head(
      "LigerBots Photos - $subtitle",
      false,
      array(
          "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css",
          "/css/gallery.css"
      )
  );
  ?>
  
  <body>
    <div id="header-ghost"></div>
    <div class="container-fluid no-side-padding">
      <div class="col-xs-12 no-side-padding">
        
        <?php
        output_header(); 
        output_navbar();
        ?>
        
        <div class="row page-body">
          <div class="col-md-12 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
            <div class="row top-spacer"></div>
            <div class="row">
              <div class="title-bar">
                <center><div class="notindex-title">
                  <?php
                  if ( isset($albumPhotos) ) // currently viewing photos in an album
                  {
                      echo "<a href=\"gallery.php?year=" . $albumPhotos['yearIndex'] . "\" style=\"color: white;\">\n";
                      echo      strtoupper( $albumPhotos["yearTitle"] ) . "\n";
                      echo "</a> \n";
                  }
                  else // viewing a year's list of albums
                  {
                      echo '<a href="/gallery.php" style="color: white;">PHOTOS</a>';
                  }
                  ?>
                </div></center><br/>
              </div>
              
              <?php
              // if in the album list, show the links to Flickr and youTube
              if ( !isset($albumPhotos) )
              {
                  echo "<center style=\"margin-bottom: 2.25em; word-wrap: break-word;\">\n"; // add proper margins & allow the youtube url to be wrapped on mobile
                  echo "        <h5 class=\"gallery-link\">To see all LigerBots photos: ";
                  echo "                <a href=\"https://www.flickr.com/photos/ligerbots/\" target=\"_blank\">flickr.com/photos/ligerbots/</a>";
                  echo "        </h5>\n";
                  echo "        <h5 class=\"gallery-link\">To see all LigerBots videos: ";
                  echo "                <a href=\"https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA\" target=\"_blank\">youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>";
                  echo "        </h5>";
                  echo "</center>\n";
              }
              ?>
              
              <div class="gallery-container">
                <?php
                // the url has no album display specification; show the year view
                if ( !isset($_GET["album"]) )
                {
                    albumListDisplay($albumList, $year);
                }
                else
                {
                    albumDisplay($albumPhotos);
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script type="text/javascript">
     $.fancybox.defaults.slideShow = false;
     $.fancybox.defaults.fullScreen = false;
     function sizeImage(imageDiv) {
         var image = $(imageDiv).find("img");
         var aspectRatio = image.naturalHeight / image.naturalWidth;
         if (aspectRatio <= 1) {
             $(image).addClass('gallery-photo-wide');
             $(imageDiv).removeClass('gallery-photo-loading');
         } else {
             $(image).addClass('gallery-photo-tall');
             $(imageDiv).removeClass('gallery-photo-loading');
         }
     }
     $(document).ready(
         function(){
             $(".gallery-photo-loading").each(function(){ sizeImage( $(this) ); } );
         }
     );
    </script>
  </body>
</html>
