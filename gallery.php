<?php
	require_once( "include/page_elements.php" );
	http_response_code(200); //ovveride WordPress; this page *does* exist
	require_once("include/gallery-utils.php");
	$flickr = createFlickr();
	
	if ( isset($_GET["album"]) )
	{
		//viewing an album; get its photos
		$albumPhotos = getPhotoList( $flickr, $_GET["album"] );
	}
	else
	{
		if ( isset($_GET["year"]) )
		{
			//viewing a specific year; use that
			$year = $_GET[ "year" ];
		}
		else
		{
			//no specific year; default to most recent
			$year = 0;
		}
		$albumList = getAlbums( $flickr );
	}
?>


<!DOCTYPE html>
<html>
	<?php
		page_head(
			"LigerBots Gallery",
			false,
			array(
				"https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css",
				"/css/gallery.css"
			)
		);
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
						<div class="row">
							<div class="title-bar">
								<div class="notindex-title">
									<a href="/" class="breadcrumbs-link">
										<span class="breadcrumbs-link glyphicon glyphicon-home" style="font-size: 12pt; top: 1.5pt;"></span>
									</a>
									<span class="glyphicon glyphicon-chevron-left breadcrumbs-chevron-1"></span>
									<span class="glyphicon glyphicon-chevron-left breadcrumbs-chevron-2"></span>
									<a href="/gallery.php?year=0" class="breadcrumbs-link">
										PHOTOS
									</a>
									<span class="glyphicon glyphicon-chevron-left breadcrumbs-chevron-1"></span>
									<span class="glyphicon glyphicon-chevron-left breadcrumbs-chevron-2"></span>
									<?php
										if ( isset($albumPhotos) ) //currently viewing photos in an album
										{
											echo "<a href=\"gallery.php?year=" . $albumPhotos['yearIndex'] . "\" class=\"breadcrumbs-link\">\n";
											echo	strtoupper( $albumPhotos["yearTitle"] ) . "\n";
											echo "</a> \n";
											echo "<span class=\"glyphicon glyphicon-chevron-left breadcrumbs-chevron-1\"></span>\n";
											echo "<span class=\"glyphicon glyphicon-chevron-left breadcrumbs-chevron-2\"></span>\n";
											echo strtoupper( $albumPhotos["title"] ) . "\n";
										}
										else //viewing a year's list of albums
										{
											echo strtoupper( $albumList[$year]["title"] );
										}
									?>
								</div>
							</div>
							
							<?php
								// if in the album list, show the links to Flickr and youTube
								if ( !isset($albumPhotos) )
								{
									echo "<center style=\"margin-bottom: 3em; word-wrap: break-word;\">\n"; //add proper margins & allow the youtube url to be wrapped on mobile
									echo "	<h5 class=\"gallery-link\">To see all LigerBots photos: ";
									echo "		<a href=\"https://www.flickr.com/photos/ligerbots/\" target=\"_blank\">flickr.com/photos/ligerbots/</a>";
									echo "	</h5>\n";
									echo "	<h5 class=\"gallery-link\">To see all LigerBots videos: ";
									echo "		<a href=\"https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA\" target=\"_blank\">youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>";
									echo "	</h5>";
									echo "</center>\n";
								}
							?>
							
							<div class="gallery-container">
								<?php
									//the url has no album display specification; show the year view
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
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
		<script type="text/javascript">
			$.fancybox.defaults.slideShow = false;
			$.fancybox.defaults.fullScreen = false;
			function sizeImage(imageDiv) {
				var image = $(imageDiv).find("img");
				var aspectRatio = image.naturalHeight / image.naturalWidth;
				if (aspectRatio <= 1)
				{
					$(image).addClass('gallery-photo-wide');
					$(imageDiv).addClass('gallery-photo-wide');
					$(imageDiv).removeClass('gallery-photo-loading');
				}
				else
				{
					$(image).addClass('gallery-photo-tall');
					$(imageDiv).addClass('gallery-photo-tall');
					$(imageDiv).removeClass('gallery-photo-loading');
				}
			}
			$(document).ready(
				function(){
					$(".gallery-photo-loading").each(
						function(){
							sizeImage( $(this) ); 
						}
					);
				}
			);
		</script>
	</body>
</html>
