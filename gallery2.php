<?php
	require_once( "include/page_elements.php" );
	require_once( "include/utils.php" );
	http_response_code(200); // no wordpress, this isn't an error
	
	// not sure if we're allowed to dump cache files in /tmp and /var/tmp
	define("CACHE_ALLOWED", false);

	//get the flickr api
	require_once("include/phpflickr/phpFlickr.php");
	$flickr = new phpFlickr(FLICKR_API_KEY);
	if(CACHE_ALLOWED) {
		$flickr->enableCache("fs", "/var/tmp");
	}
	
	//get the flickr info
	$photosetID = $_GET['album_id'];
	$getAlbumResponse = $flickr->call('flickr.photosets.getPhotos', array('photoset_id' => $photosetID, 'user_id' => "127608154@N06", 'extras' => "url_sq,url_m"));
	
	//make sure that worked
	if(!$getAlbumResponse) {
		error_log("Flickr error on photosets_getPhotos: " . $flickr->error_msg);
		http_response_code(500);
	}
	
	//get the albums
	$allAlbums = $flickr->photosets_getList("127608154@N06");
	
	//make sure we got the albums
	if(!$allAlbums) {
		error_log("Flickr error on photosets_getList: " . $flickr->error_msg);
	} else {
		//make an array to put the albums in
		$sortedAlbums = array();
		//if it worked, sort the albums by year
		foreach($allAlbums['photoset'] as $album) {
			$albumName = $album['title']['_content'];
			//get the album's year
			$albumYear = date("Y", intval($album['date_create']));
			//not sure what this does, something about getting the year
			$matches;
			$match_success = preg_match("/20[0-9]{2}/", $albumName, $matches);
			if($match_success) {
				$albumYear = $matches[0];
			}
			
			//if this year isn't already in the list, add the album
			if(!$sortedAlbums[$albumYear]) {
				$sortedAlbums[$albumYear] = array();
				$sortedAlbums[$albumYear][] = array("name" => $albumName, "year" => $albumYear, "id" => $album["id"]);
			}
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
					<?php
						foreach($sortedAlbums as $albumYear=>$album) {
							?>
							<div id="<?=$albumYear;?>">
								<p><?=$albumYear;?></p>
								<?php
									//https://www.flickr.com/services/api/flickr.photos.getInfo.html
									?>
									<img src="" />
									<?php
									}
								?>
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