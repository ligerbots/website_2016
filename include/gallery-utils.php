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
                            'thumb' => getPhotoUrl( $albumInfo["photoset"][$albumKey], 'primary', 'n' ) );
            $yearSet['albums'][] = $aInfo;  // append
        }
        $albumList[] = $yearSet;
    }

    return $albumList;
}

function getPhotoUrl($info, $photoIndex, $sizeLetter)
{
    $photoUrl = "https://farm".$info["farm"].".staticflickr.com/".$info["server"]."/".$info[$photoIndex]."_".$info["secret"]."_$sizeLetter.jpg"; //n gives longest side = 320px
    return $photoUrl;
}

function getAlbumTitle($flickr, $albumID)
{
    $info = $flickr->photosets_getInfo( $albumID, FLICKR_USERID );
    return $info['title']['_content'];
}

// Return results is a list of dictionares.
function getPhotoList($flickr, $albumID)
{
    $albumInfo = $flickr->photosets_getPhotos( $albumID, 'tags' );
    
    $taggedPhotos = array();
    $randomPhotos = array();

    $lenAlbum = count( $albumInfo["photoset"]["photo"] );
    $needRandom = true;
    
    foreach ( $albumInfo["photoset"]["photo"] as $photo )
	{
        // pick photos which have tag "website" (among other tags)
        if ( preg_match('/\\bwebsite\\b/i', $photo["tags"]) )
		{
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

    if ( count( $taggedPhotos ) > 0 )
        return $taggedPhotos; 
    // no photos have tags; return the random ones
    return $randomPhotos;
}
