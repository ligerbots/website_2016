<?php
// Routines to query Flickr for the LigerBots gallery page

// for now, no caching. Turn on later
define( "CACHE_ALLOWED", false );

// TEMPORARY: for testing
// FLICKR_API_KEY will be set from config when running on the server
if ( ! defined( 'FLICKR_API_KEY' ) )
    define( 'FLICKR_API_KEY', getenv( 'FLICKR_API_KEY' ) );
if ( ! defined( 'FLICKR_API_KEY' ) )
{
    print "ERROR: FLICKR_API_KEY is not defined\n";
    throw new Exception( 'FLICKR_API_KEY is not defined. Set an environment variable?' );
}

require_once( "phpflickr/phpFlickr.php" );
// our user id
define( 'FLICKR_USERID', '127608154@N06' );

// Create a Flickr API handler. Do this here to keep everything in one file.
// Will eventually include a bit more setup, like caching.
function createFlickr()
{
    $flickr = new phpFlickr(FLICKR_API_KEY);
    /* if(CACHE_ALLOWED) {
     *     $f->enableCache("fs", "/var/tmp");
     * }
     */
    return $flickr;
}

// Query for the list of collections and albums in each collection
function getAlbumTree( $flickr )
{
    // print "Calling gettree\n";
    $albumList = $flickr->collections_getTree( NULL, FLICKR_USERID );
    // print $flickr->getErrorCode() ."\n";
    // print $flickr->getErrorMsg() ."\n";
    
    // Strip off some unneeded layers in the structure
    return $albumList['collections']['collection'];
}

function getPrimaryPhoto( $flickr, $albumID ) {
    $albumInfo = $flickr->photosets_getInfo( $albumID, FLICKR_USERID );
    $photoURL = getPhotoUrl( $albumInfo, "primary", "n" );   //n gives image with longest side = 320px
    return $photoURL;
}

function getPhotoUrl( $info, $photoIndex, $sizeLetter ) {
    $photoUrl = "https://farm".$info["farm"].".staticflickr.com/".$info["server"]."/".$info[$photoIndex]."_".$info["secret"]."_$sizeLetter.jpg"; //n gives longest side = 320px
    return $photoUrl;
}

// Return results is a list of dictionares.
function getPhotoList( $flickr, $albumID ) {
    $albumInfo = $flickr->photosets_getPhotos( $albumID, 'tags' );

    $taggedPhotos = array();
    $randomPhotos = array();

    $lenAlbum = count( $albumInfo["photoset"]["photo"] );
    $needRandom = true;
    
    foreach ( $albumInfo["photoset"]["photo"] as $photo ) {
        // pick photos which have tag "website" (among other tags)
        if ( preg_match( '/\\bwebsite\\b/i', $photo["tags"] ) ) {
            // small and big
            $taggedPhotos[] = array( "small" => getPhotoUrl( $photo, "id", "n" ),
                                     "large" => getPhotoUrl( $photo, "id", "b" ) );
            $needRandom = false;
        }
        else if ( $needRandom && rand( 0, $lenAlbum ) < 12 )
        {
            $randomPhotos[] = array( "small" => getPhotoUrl( $photo, "id", "n" ),
                                     "large" => getPhotoUrl( $photo, "id", "b" ) );
        }
    }

    // no photos have tags; pick 10 random ones
    if ( count( $taggedPhotos ) > 0 )
        return $taggedPhotos;
    return $randomPhotos;
}

// Testing code!!!!
//$flickr = createFlickr();
//$albumList = getAlbumTree( $flickr );
// pretty print the data structure
//print_r( $albumList );
?>
