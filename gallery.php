<?php
require_once( "include/page_elements.php" );
http_response_code(200); // override wp

require_once("include/gallery-utils.php");
$flickr = createFlickr();
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
                <center><div class="notindex-title">PHOTOS</div></center>
                <br/>
                
                <?php
                //the url has no album display specification; show the year view
                if ( ! isset( $_GET["album"] ) ) {
                    echo "<center>\n";
                    echo 'LigerBots Flickr: <a href="https://www.flickr.com/photos/ligerbots/">flickr.com/photos/ligerbots/</a><br/>' . "\n";
                    echo 'LigerBots Videos: <a href="https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA">youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>' . "\n";
                    echo "</center>\n";
                    echo "<br/>\n";
                    
                    $albumList = getAlbums($flickr);
                    $count = 0;
                    foreach ( $albumList as $year )
                    {
                        echo '<div class="row row-margins">' . "\n";
                        // the year header
                        echo '  <div class="level4-heading">' . $year["title"] . "</div>\n";
                        // create each album box
                        foreach ( $year[ 'albums' ] as $album )
                        {
                            echo '<a href="gallery.php?album=' . $album["id"] . '">' . "\n";
                            echo '  <div class="gallery-thumbnail" style="background: url(' . $album["thumb"] . ') 50% 50% no-repeat; background-size: cover;">' . "\n";
                            echo '    <div class="gallery-caption">' . $album["title"] . "</div>\n";
                            echo "  </div>\n";
                            echo "</a>\n";
                            $count += 1;
                            //if ( $count >= 4 ) break;
                        }
                        echo "</div>\n";

                        //break;
                    }
                } else {
                    echo '<div class="row row-margins">' . "\n";
                    // there is an album to display; show as a list of photos
                    $albumId = $_GET["album"];
                    echo '  <div class="level4-heading">' . getAlbumTitle( $flickr, $albumId ) . "</div>\n";

                    echo '  <center>See the full album on Flickr: <a href="https://www.flickr.com/photos/ligerbots/albums/' . $albumId . '">';
                    echo 'https://www.flickr.com/photos/ligerbots/albums/' . $albumId . "</a></center>\n";
                    echo "<br/>\n";
                    
                    // a list of urls for each photo in the album
                    $photoInfoList = getPhotoList( $flickr, $albumId );
                    foreach ( $photoInfoList as $photoInfo ) {
                        echo '<a data-fancybox="gallery" href="' . $photoInfo["large"] .'">' . "\n";
                        echo '  <div class="gallery-thumbnail" style="background: url(' . $photoInfo["small"] . 
                             ') 50% 50% no-repeat; background-size: cover;"></div>' . "\n";
                        echo "</a>\n";
                    }
                    echo "</div>\n";                    
                }
                ?>
                
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
      </script>
  </body>
</html>
