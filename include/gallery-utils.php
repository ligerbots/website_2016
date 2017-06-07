<?php
// Routines to query Flickr for the LigerBots gallery page
//define( "CACHE_ALLOWED", false );
define( "CACHE_ALLOWED", true );
// TEMPORARY: for testing
// FLICKR_API_KEY will be set from config when running on the server
if ( !defined('FLICKR_API_KEY') )
{
	define( 'FLICKR_API_KEY', getenv('FLICKR_API_KEY') );
}
if ( !defined('FLICKR_API_KEY') ) //make sure it worked
{
	print "ERROR: FLICKR_API_KEY is not defined\n";
	throw new Exception( 'FLICKR_API_KEY is not defined. Set an environment variable?' );
}
require_once("phpflickr/phpFlickr.php");
// our user id
define('FLICKR_USERID', '127608154@N06');
// Create a Flickr API handler. Do this here to keep everything in one file.
// Will eventually include a bit more setup, like caching.
function createFlickr()
{
	$flickr = new phpFlickr( FLICKR_API_KEY );
	if ( CACHE_ALLOWED )
	{
		$dbURI = 'mysql://' . DB_USER . ':' . DB_PASSWORD . '@' . DB_HOST . '/' . DB_NAME;
		$flickr->enableCache( 'db', $dbURI, 3600 );
	}
	return $flickr;
}


//////////////
//GET ALBUMS//
//////////////


// Query for the list of collections and albums in each collection
function getAlbums($flickr)
{
	$albumInfo = $flickr->photosets_getList( FLICKR_USERID ); // get a list of all LigerBots albums
	//make an array that stores each album id compared to its index
	//this allows the getList and getTree data to be combined
	$albumKeyPairs = array();
	$index = 0;
	foreach ( $albumInfo["photoset"] as $album )
	{
		$albumKeyPairs[ $album["id"] ] = $index;
		$index++;
	}
	$allAlbums = $flickr->collections_gettree(NULL, FLICKR_USERID);
	
	$finalData = array(); //will contain all the new combined info
	foreach ( $allAlbums["collections"]["collection"] as $year )
	{
		$yearData = array( "title" => $year["title"],
		                   "albums" => array() );
		foreach ( $year["set"] as $album )
		{
			$albumKey = $albumKeyPairs[ $album["id"] ];
			$yearData["albums"][] =  array( "id" => $album["id"],
			                                "title" => $album["title"],
			                                "thumb" => getPhotoUrlStub( $albumInfo["photoset"][$albumKey], "primary" ) );
		}
		$finalData[] = $yearData;
	}
	return $finalData;
}


/////////////////////
//DATA INTERPRETERS//
/////////////////////


function getPhotoUrlStub($info, $photoIndex)
{
	//stub doesn't include ending like c.jpg or s.jpg
	return "https://farm".$info["farm"].".staticflickr.com/".$info["server"]."/".$info[$photoIndex]."_".$info["secret"]; 
}
function getAlbumInfo($flickr, $albumID)
{
	$info = $flickr->photosets_getInfo( $albumID, FLICKR_USERID );
	$res = array( 'title' => $info['title']['_content'],
	              'description' => $info['description']['_content'] );
	return $res;
}
function getPhotoDescription( $flickr, $photoID, $secret )
{
	$info = $flickr->photos_getInfo( $photoID, $secret );
	return $info[ 'photo' ][ 'description' ][ '_content' ];
}


//////////////////
//GET PHOTO LIST//
//////////////////


// Return results is a list of dictionares.
function getPhotoList( $flickr, $albumID )
{
	$finalData = null;
	// Need a bunch of info about the album itself
	$albumsInYears = $flickr->collections_gettree( NULL, FLICKR_USERID );
	$albumsInYears = $albumsInYears[ 'collections' ][ 'collection' ]; //strip off unnecessary data
	// search through the tree for matching data & get indexes to find future data
	$yearIndex = 0;
	foreach ( $albumsInYears as $currentYear )
	{
		$albumIndex = 0;
		foreach ( $currentYear['set'] as $currentAlbum )
		{
			if ( $currentAlbum['id'] == $albumID )
			{
				//we found the right one; get all the info
				$finalData = array(
					'title' => $currentAlbum[ 'title' ],
					'desc' => $currentAlbum[ 'description' ],
					'albumId' => $currentAlbum[ 'id' ],
					'yearIndex' => $yearIndex,
					'yearTitle' => $currentYear[ 'title' ],
					'albumIndex' => $albumIndex );
				break 2; //we have all the info. We're done here.
			}
		$albumIndex++;
		}
		
		$yearIndex++;
	}
	// Need the list of albums in this 'year' for the dropdown menu
	$albumList = array();
	foreach ( $albumsInYears[ $yearIndex ][ 'set' ] as $album )
	{
		$albumList[] = array( 'title' => $album['title'],
		                      'id'    => $album['id'] );
	}
	$finalData[ 'albums' ] = $albumList;
	// Now, get the photos to show
	// Compile a list of both tagged photos and a random set,
	//   and return the tagged one if there are some.
	// NOTE: this method is quick because it is only 1 pass and does not
	//  involve rebuilding the list. Also it keeps the photos in time order.
	
	$albumInfo = $flickr->photosets_getPhotos( $albumID, 'tags' );
	$photosRemaining = count( $albumInfo['photoset']['photo'] );
	$photosNeeded = 10; //how many photos to show
	$photosPicked = array(); //all the photos we've picked
	$areTaggedPhotos = false;
	
	foreach ( $albumInfo['photoset']['photo'] as $photo )
	{
		// pick photos which have tag 'website' (among other tags)
		// Use 'title' as the caption for now
		if ( preg_match('/\\bwebsite\\b/i', $photo['tags']) )
		{
			if (!$areTaggedPhotos) //we found the first tagged photo
			{
				$photosUsed = array(); //clear out any randomly selected photos
			}
			$photosPicked[] = $photo; //add it to the list of used photos so we don't pick it again
			$areTaggedPhotos = true; //there are now tagged photos; don't pick ones randomly or clear the list
		}
		//only pick random photos if we haven't found any tagged photos yet
		else if ( !$areTaggedPhotos && rand( 0, $photosRemaining ) < $photosNeeded ) //not sure what the point of the rand() is
		{
			$photosPicked[] = $photo;
			$photosNeeded--;
		}
		$photosRemaining--; //not sure why we are decrementing both this and photosNeeded
	}
	// Need to call to Flickr to get the photo descriptions. Bad API ;-(
	$photoList = array();
	foreach( $photosPicked as $photo )
	{
		$photoList[] = array(
			'url_stub' => getPhotoUrlStub( $photo, 'id' ),
			'caption' => getPhotoDescription( $flickr, $photo['id'], $photo['secret'] )
		);
	}
	$finalData[ 'photos' ] = $photoList;
	return $finalData;
}


//////////////
//ADD BUTTON//
//////////////


// ---------------------------------
// Page layout functions
function addButton( $name, $ref, $left, $top )
{
	if ($top) // buttons at the top
	{
		if ($left) {
			echo "<div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-2\">\n";
		} else { //button on the right needs to move based on viewport width, whether or not the dropdown gets it's own line
			echo "<div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-2 col-sm-push-6 col-md-push-6 col-lg-push-8\">\n";
		}
	} else { // buttons at the bottom
		echo "<div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-3\">\n";
	}
	
	if ( !is_null($ref) ) //button goes somewhere
	{
		if ( $left )
		{
			echo "<a style=\"float: left;\" class=\"gallery-nav-button\" href=\"/gallery.php?$ref\">\n";
			echo "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
			echo "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
			echo " $name"; // added space to account for decreased spacing on chevrons
		} else { // right side
			echo "<a style=\"float: right;\" class=\"gallery-nav-button\" href=\"/gallery.php?$ref\">\n";
			echo $name; //no space needed, text spacing is right-forward, not center-outward
			echo "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
			echo "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
		}
	}
	else //button is disabled
	{
		if ( $left )
		{
			echo "<a style=\"float: left;\" class=\"gallery-nav-button-disabled\">\n";
			echo "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
			echo "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
			echo " $name";
		} else {
			echo "<a style=\"float: right;\" class=\"gallery-nav-button-disabled\">\n";
			echo $name;
			echo "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
			echo "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
		}
	}
	echo "  </a>\n";
	echo "</div>\n";
}


////////////////
//ADD DROPDOWN//
////////////////


function addDropdown( $itemList, $itemIndex, $isYear )
{
	echo "<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-8 col-sm-pull-3 col-md-pull-3 col-lg-pull-2\">\n"; //dropdown needs to move based on viewport, whether or not it gets it's own line
	echo "  <div class=\"gallery-dropdown\">\n";
	echo "    <button class=\"btn btn-primary dropdown-toggle gallery-dropdown-button\" type=\"button\" data-toggle=\"dropdown\">\n";
	echo $itemList[$itemIndex]["title"]."  \n";
	echo "      <div style=\"width: 5pt; display: inline-block;\"></div>"; //spacer before triangle
	echo "      <span class=\"glyphicon glyphicon-triangle-bottom\"></span>\n";
	echo "    </button>\n";
	echo "    <ul class=\"dropdown-menu gallery-dropdown-content\">\n";
	
	$currentItem = 0;
	if ( $isYear ) //data sources for year and album are stored differently
	{
		foreach ( $itemList as $item )
		{
			if ( $currentItem != $itemIndex ) //normal DD item
			{
				echo "<li><a href=\"/gallery.php?year=$currentItem\" class=\"gallery-dropdown-item\">\n";
			} else { //make item 'selected' to indicate the current position
				echo "<li><a class=\"gallery-dropdown-item-active active\">\n";
			}
			echo $item["title"]."\n";
			echo "</a></li>\n";
			$currentItem++;
		}
	}
	else
	{
		foreach ( $itemList as $item )
		{
			if ( $currentItem != $itemIndex )
			{
				echo "<li><a href=\"/gallery.php?album={$item["id"]}\" class=\"gallery-dropdown-item\">\n";
			} else {
				echo "<li><a class=\"gallery-dropdown-item-active active\">\n";
			}
			echo $item["title"] . "\n";
			echo "</a></li>\n";
			$currentItem++;
		}
	}
	
	echo "    </ul>\n";
	echo "  </div>\n";
	echo "</div>\n";
}


//////////////////////
//ALBUM LIST DISPLAY//
//////////////////////


function albumListDisplay( $albumList, $year )
{
	//note: albums are ordered most recent (index 0) to oldest
	if ( $year < count($albumList) - 1 )
		//set buttons to the proper reference
		{ $nextLink = "year=" . ($year + 1 ); }
	else
		//no more albums in this direction
		{ $nextLink = null; }
	if ( $year > 0 )
		{ $prevLink = "year=" . ( $year - 1 ); }
	else
		{ $prevLink = null; }
	
	//buttons and header
	echo "<div class=\"row gallery-buttons-bar-container-top\">\n";
	addButton( "Previous year", $prevLink, true, true );
	addButton( "Next year", $nextLink, false, true);
	addDropdown( $albumList, $year, 1 ); //dropdown needs to come after; shifts to the middle on larger screens
	echo "</div>\n";
	
	// create each album box
	echo "<div class=\"gallery-content\">\n";
	foreach ( $albumList[ $year ][ "albums" ] as $currentAlbum )
	{
		echo "<a href=\"/gallery.php?album={$currentAlbum["id"]}\" style=\"text-decoration: none;\">\n";
		echo "  <div class=\"gallery-thumbnail\" style=\"background: url({$currentAlbum["thumb"]}_z.jpg) 50% 50% no-repeat; background-size: cover;\">\n";
		echo "      <div class=\"gallery-caption\">\n";
		echo            $currentAlbum["title"]."\n";
		echo "      </div>\n";
		echo "  </div>\n";
		echo "</a>\n";
	}
	echo "</div>\n";
	
	//bottom nav buttons
	echo "<div class=\"row gallery-buttons-bar-container-bottom\">\n";
	addButton( "Previous year", $prevLink, true, false );
	echo "<div class=\"col-sm-6 col-md-6 col-lg-6\"></div>\n";
	addButton( "Next year", $nextLink, false, false );
	echo "</div>\n";
}


/////////////////
//ALBUM DISPLAY//
/////////////////


function albumDisplay( $albumPhotos )
{
	if ( $albumPhotos["albumIndex"] > 0 )
		{ $nextink = "album=" . $albumPhotos["albums"][ $albumPhotos["albumIndex"] - 1 ]["id"]; }
	else
		{ $nextLink = null; }
	if ( $albumPhotos["albumIndex"] < count( $albumPhotos["albums"] ) - 1 )
		{ $prevLink = "album=" . $albumPhotos["albums"][ $albumPhotos["albumIndex"] + 1 ]["id"]; }
	else
		{ $prevLink = null; }
	
	// Top buttons and header
	echo "<div class=\"row gallery-buttons-bar-container-top\">\n";
	addButton( "Previous album", $prevLink, true, true );
	addButton( "Next album", $nextLink, false, true );
	addDropdown( $albumPhotos[ "albums" ], $albumPhotos[ "albumIndex" ], 0 );
	echo "</div>\n";
	echo "<div class=\"gallery-album-description\">\n";
	if ( strlen( $albumPhotos[ "desc" ] ) > 0 ) echo '<p>' . $albumPhotos[ "desc" ] . "</p>\n";
	echo '<p>See the full album on Flickr: ';
	echo '<a href="https://www.flickr.com/photos/ligerbots/albums/' . $albumPhotos[ 'albumId' ] . '" target="_blank">';
	echo 'flickr.com/photos/ligerbots/albums/' . $albumPhotos[ 'albumId' ] . "</a></p>\n";
	echo "</div>\n";
	
	// Output the photos in two columns
	$index = 0;
	$colBreak = ceil( count( $albumPhotos[ "photos" ] ) / 2 ); 
        // need row class to keep bottom-nav-bar from wrapping into blank space below last photo
        echo "<div class=\"row row-margins\" style=\"margin-left: 0; margin-right: 0; \">\n"; //row-margins doesn't seem to be reverting the negative margins, temporary manual override
	echo "<div class=\"gallery-photo-column col-xs-12 col-sm-6 col-md-6 col-lg-6\">\n";
	foreach ( $albumPhotos["photos"] as $photo )
	{
		echo "<a data-fancybox=\"gallery\" href=\"" . $photo["url_stub"] . "_b.jpg\">\n";
		echo "  <div class=\"gallery-photo-container gallery-photo-loading\">\n";
		echo "    <img src=\"" . $photo["url_stub"] . "_z.jpg\">\n";
		echo "    <div class=\"gallery-photo-desc\">" . $photo["caption"] . "</div>\n";
		echo "  </div>\n";
		echo "</a>\n";
		$index++;
		if ( $index == $colBreak )
		{
			// close the left column, start the right column
			echo "</div>\n";
			echo "<div class=\"gallery-photo-column col-xs-12 col-sm-6 col-md-6 col-lg-6\">\n";
		}
	}
	echo "</div>\n";
	echo "</div>\n";
	
	// Bottom nav buttons
	echo "<div class=\"row gallery-buttons-bar-container-bottom\">\n";
	addButton( "Previous album", $prevLink, true, false );
	echo "<div class=\"col-sm-6 col-md-6 col-lg-6\"></div>\n"; //spacer
	addButton( "Next album", $nextLink, false, false );
	echo "</div>\n";
}
?>
