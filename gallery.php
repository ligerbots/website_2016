<?php
    require_once( "include/page_elements.php" );
    require_once( "include/utils.php" );
    http_response_code(200); // no wordpress, this isn't an error
    
    // not sure if we're allowed to dump cache files in /tmp and /var/tmp
    define("CACHE_ALLOWED", false);
    
    function endswith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }
    
    require_once("include/phpflickr/phpFlickr.php");
    $f = new phpFlickr(FLICKR_API_KEY);
    if(CACHE_ALLOWED) {
        $f->enableCache("fs", "/var/tmp");
    }
    
    if(isset($_GET['album_id'])) {
        $id = $_GET['album_id'];
        $res = $f->photosets_getPhotos($id, "127608154@N06");
        if(!$res) {
            error_log("Flickr error on photosets_getPhotos: " . $f->error_msg);
            http_response_code(500);
        } else {
            header("Content-Type: application/json");
            echo json_encode($res);
        }
        die();
    } else if(isset($_GET['photo_id'])) {
        $cacheFile = "f_" . $_GET['photo_id'];
        if(isset($_GET['thumb'])) {
            $cacheFile .= "_thumb";
        }
        
        header("Content-Type: image/jpeg");
        
        if(CACHE_ALLOWED && file_exists("/tmp/" . $cacheFile) && exif_imagetype("/tmp/" . $cacheFile)) {
            header("Content-Length: " . filesize("/tmp/" . $cacheFile));
            echo file_get_contents("/tmp/" . $cacheFile);
            die();
        }
        $res = $f->photos_getSizes($_GET['photo_id']);
        if(!$res) {
            error_log("Flickr error on photosets_getPhotos: " . $f->error_msg);
            http_response_code(500);
        } else {
            $thumb;
            $original;
            foreach($res as $resolution) {
                if($resolution['label'] == "Square") {
                    $thumb = $resolution;
                }
                if($resolution['label'] == "Large") {
                    $original = $resolution;
                }
            }
            $source;
            if(isset($_GET['thumb'])) {
                $source = $thumb['source'];
            } else {
                $source = $original['source'];
            }
            
            $contents = file_get_contents($source);
            header("Content-Length: " . strlen($contents));
            if(CACHE_ALLOWED) {
                file_put_contents("/tmp/" . $cacheFile, $contents);
            }
            echo $contents;
        }
        die();
    }
    
    $albums = $f->photosets_getList("127608154@N06");
    $albumsByYear = array();
    if(!$albums) {
        error_log("Flickr error on photosets_getList: " . $f->error_msg);
    } else {
        foreach($albums['photoset'] as $album) {
            $name = $album['title']['_content'];
            // find the year of the album in the title. Otherwise default to created date
            $year = date("Y", intval($album['date_create']));
            $matches;
            $match_success = preg_match("/20[0-9]{2}/", $name, $matches);
            if($match_success) {
                $year = $matches[0];
            }
            
            if(!$albumsByYear[$year]) {
                $albumsByYear[$year] = array();
            }
            
            $albumsByYear[$year][] = array("name" => $name, "year" => $year, "id" => $album["id"]);
        }
    }
?>

<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Gallery", false, "/css/gallery.css" ); ?>

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
            <div class="row side-margins row-margins bottom-margin">
                <center><div class="notindex-title" style="margin-bottom: 3rem;">GALLERY</div></center>
                <p><center>
                    LigerBots Flickr: <a href="https://www.flickr.com/photos/ligerbots/" target="_blank">https://www.flickr.com/photos/ligerbots/</a><br/>
                    LigerBots Videos: <a href="https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA" target="_blank">https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>
                </center></p>
                <div id="gallery-browse">
                    <div id="gallery-sidebar">
                        <div id="gallery-year-select">
                            <select>
                                <?php
                                    foreach($albumsByYear as $year=>$album) {
                                        ?>
                                        <option value="<?=$year;?>"><?=$year;?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                            <script type="text/javascript">
                                window.albumsByYear = <?=json_encode($albumsByYear);?>;
                            </script>
                        </div>
                        <div id="gallery-album-select">
                            
                        </div>
                    </div>
                    <div id="gallery-items" class="loading">
                        <div class="current">
                        </div>
                        <div class="list">
                            <div class="list-inner"></div>
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
