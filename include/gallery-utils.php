<?php
// Routines to query Flickr for the LigerBots gallery page

// for now, no caching. Turn on later
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
    $albumList = array(); //will contain all the new combined info
    foreach ( $allAlbums["collections"]["collection"] as $year )
    {
        $yearSet = array( "title" => $year["title"],
                          "albums" => array() );
        foreach ( $year["set"] as $album )
        {
            $albumKey = $albumKeyPairs[ $album["id"] ];
            $aInfo = array( 'id' => $album[ 'id' ],
                            'title' => $album["title"],
                            'thumb' => getPhotoUrlStub( $albumInfo["photoset"][$albumKey], 'primary' ) );
            $yearSet['albums'][] = $aInfo;  // append
        }
        $albumList[] = $yearSet;
    }

    return $albumList;
}

function getPhotoUrlStub($info, $photoIndex)
{
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
                $finalData = array(
                    'title' => $currentAlbum[ 'title' ],
                    'desc' => $currentAlbum[ 'description' ],
                    'albumId' => $currentAlbum[ 'id' ],
                    'yearIndex' => $yearIndex,
                    'yearTitle' => $currentYear[ 'title' ],
                    'albumIndex' => $albumIndex );
                break;
            }
            $albumIndex++;
        }
        if ( ! is_null( $finalData ) ) break;
        
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

    $remaining = count( $albumInfo['photoset']['photo'] );
    $needed = 10; // show 10 random photos
    $photosOut = array();
    $isRandom = true;
    
    foreach ( $albumInfo['photoset']['photo'] as $photo )
    {
        // pick photos which have tag 'website' (among other tags)
        // Use 'title' as the caption for now
        if ( preg_match('/\\bwebsite\\b/i', $photo['tags']) )
        {
            if ( $isRandom ) $photosOut = array();
            $photosOut[] = $photo;
            $isRandom = false;
        }
        else if ( $isRandom && rand( 0, $remaining ) < $needed )
        {
            $photosOut[] = $photo;
            $needed--;
        }
        $remaining--;
    }

    // Need to call to Flickr to get the photo descriptions. Bad API ;-(
    $photoList = array();
    foreach( $photosOut as $p )
    {
        $photoList[] = array(
            'url_stub' => getPhotoUrlStub( $p, 'id' ),
            'caption' => getPhotoDescription( $flickr, $p['id'], $p['secret'] )
        );
    }
    $finalData[ 'photos' ] = $photoList;

    return $finalData;
}

// ---------------------------------
// Page layout functions


function addButton( $name, $ref, $left, $top )
{
    if ( $top ) // buttons at the top should take the full width
    {
        echo "<div class=\"col-xs-12 col-sm-3 col-md-3 col-lg-3\">\n";
    } else { // buttons at the bottom won't because the spacer disappears (no col-xs- class)
        echo "<div class=\"col-xs-6 col-sm-3 col-md-3 col-lg-3\">\n";
    }
    
    if ( ! is_null( $ref ) )
    {
        if ( $left )
        {
            echo "<a style=\"float: left;\" class=\"gallery-nav-button\" href=gallery.php?$ref>\n";
            echo "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
            echo "<span class=\"glyphicon glyphicon-chevron-left\"></span>";
            echo " $name"; // added space to account for decreased spacing on chevrons
        } else { // right side
            echo "<a style=\"float: right;\" class=\"gallery-nav-button\" href=gallery.php?$ref>\n";
            echo $name; //no space needed, text spacing is right-forward, not center-outward
            echo "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
            echo "<span class=\"glyphicon glyphicon-chevron-right\"></span>";
        }
    }
    else
    {
        // button is disabled
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

function addDropdown($itemList, $itemIndex, $isYear)
{
    echo "<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6\">\n";
    echo "  <div class=\"gallery-dropdown\">\n";
    echo "    <button class=\"btn btn-primary dropdown-toggle gallery-dropdown-button\" type=\"button\" data-toggle=\"dropdown\">\n";
    echo $itemList[$itemIndex]["title"]."  \n";
    echo "      <div style=\"width: 5pt; display: inline-block;\"></div>"; //spacer before triangle
    echo "      <span class=\"glyphicon glyphicon-triangle-bottom\"></span>\n";
    echo "    </button>\n";
    echo "    <ul class=\"dropdown-menu gallery-dropdown-content\">\n";
    
    $currentItem = 0;
    if ( $isYear )
    {
        foreach ( $itemList as $item )
        {
            if ( $currentItem != $itemIndex )
            {
                echo "<li><a href=gallery.php?year=$currentItem class=\"gallery-dropdown-item\">\n";
            } else {
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
                echo "<li><a href=gallery.php?album={$item["id"]} class=\"gallery-dropdown-item\">\n";
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

function albumListDisplay( $albumList, $year )
{
    // Note: assume albums are in reverse time order (meaning of "previous")
    if ( $year < count($albumList) - 1 )
        $prevLink = 'year=' . ($year + 1 );
    else
        $prevLink = null;
    if ( $year > 0 )
        $nextLink = 'year=' . ( $year - 1 );
    else
        $nextLink = null;
    
    echo "<div>\n";

    ////////////////////
    //BUTTONS & HEADER//
    ////////////////////
    echo "<div class=\"row gallery-buttons-bar-container-top\">\n";
    addButton( "Previous year", $prevLink, true, true );
    addDropdown( $albumList, $year, 1 );
    addButton( "Next year", $nextLink, false, true);
    echo "</div>\n";
    
    // create each album box
    echo "<div class=\"gallery-content\">\n";
    foreach ( $albumList[ $year ][ "albums" ] as $currentAlbum )
    {
        echo "<a href=\"gallery.php?album={$currentAlbum["id"]}\" style=\"text-decoration: none;\">\n";
        echo "  <div class=\"gallery-thumbnail\" style=\"background: url({$currentAlbum["thumb"]}_c.jpg) 0% 0% no-repeat; background-size: cover;\">\n";
        echo "      <div class=\"gallery-caption\">\n";
        echo            $currentAlbum["title"]."\n";
        echo "      </div>\n";
        echo "  </div>\n";
        echo "</a>\n";
    }
    echo "</div>\n";

    //////////////////////
    //BOTTOM NAV BUTTONS//
    //////////////////////
    echo "<div class=\"row gallery-buttons-bar-container-bottom\">\n";
    addButton( "Previous year", $prevLink, true, false );
    echo "<div class=\"col-sm-6 col-md-6 col-lg-6\"></div>\n";
    addButton( "Next year", $nextLink, false, false );
    echo "</div>\n";
}

function albumDisplay( $albumPhotos )
{
    if ( $albumPhotos["albumIndex"] > 0 )
        $prevLink = "album=" . $albumPhotos["albums"][ $albumPhotos["albumIndex"] - 1 ]["id"];
    else
        $prevLink = null;

    if ( $albumPhotos["albumIndex"] < count( $albumPhotos["albums"] ) - 1 )
        $nextLink = "album=" . $albumPhotos["albums"][ $albumPhotos["albumIndex"] + 1 ]["id"];
    else
        $nextLink = null;

    // Top buttons and header
    echo "<div class=\"row gallery-buttons-bar-container-top\">\n";
    addButton( "Previous album", $prevLink, true, true );
    addDropdown( $albumPhotos[ "albums" ], $albumPhotos[ "albumIndex" ], 0 );
    addButton( "Next album", $nextLink, false, true );
    echo "</div>\n";

    echo "<div class=\"gallery-album-description\">\n";
    echo '<p>' . $albumPhotos[ "desc" ] . "</p>\n";
    echo '<p>See the full album on Flickr:';
    echo '<a href="https://www.flickr.com/photos/ligerbots/albums/' . $albumPhotos[ 'albumId' ] . '">';
    echo 'flickr.com/photos/ligerbots/albums/' . $albumPhotos[ 'albumId' ] . "</a></p>\n";
    echo "</div>\n";

    // Output the photos in two columns
    $index = 0;
    $colBreak = ceil( count( $albumPhotos[ "photos" ] ) / 2 ); 

    echo "<div class=\"gallery-photo-column col-xs-12 col-sm-6 col-md-6 col-lg-6\">\n";
    foreach ( $albumPhotos["photos"] as $photo )
    {
        echo "<a data-fancybox=\"gallery\" href=\"" . $photo["url_stub"] . "_c.jpg\">\n";
        echo "  <div class=\"gallery-photo-container gallery-photo-loading\">\n";
        echo "    <img onload=\"sizeImage(this);\" src=\"" . $photo["url_stub"] . "_c.jpg\">\n";
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

    // Bottom nav buttons
    echo "<div class=\"row gallery-buttons-bar-container-bottom\">\n";
    addButton( "Previous album", $prevLink, true, false );
    echo "<div class=\"col-sm-6 col-md-6 col-lg-6\"></div>\n"; //spacer
    addButton( "Next album", $nextLink, false, false );
    echo "</div>\n";
}
#$flickr = createFlickr();
#$r = getPhotoList( $flickr, '72157681956375875' );
#print_r( $r );

?>
