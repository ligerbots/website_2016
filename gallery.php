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
				<div id="gallery" style="z-index: 1;">
					<div id="gallery-2015" style="z-index: 2;">
						<div id="gallery-2015-buildseason" style="z-index: 3;">
							<img src=//c6.staticflickr.com/8/7396/16384591885_5c14c19192_c.jpg style="z-index: 4;">
							<div id="gallery-2015-buildseason-caption" style="position: absolute; bottom: 0; z-index: 4;">
								<p style="align: center;">This is a test caption</p>
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
