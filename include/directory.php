<?php

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

function facebookUpload( $filelist )
{
    $c = count( $filelist['name'] );
    $msg = "";
    
    for ( $i=0; $i<$c; $i++ )
    {
        $fname = $filelist['name'][$i]; 
        $tmpName = $filelist['tmp_name'][$i];
        if ( ! is_uploaded_file( $filelist['tmp_name'][$i] ) )
        {
            $msg .= "Error: $fname is not an uploaded file.<br/>\n";
            continue;
        }

        $t = $filelist['type'][$i];
        if ( ! ( preg_match( '/^image\/p?jpeg$/i', $t ) or preg_match( '/^image\/gif$/i', $t )
            or preg_match( '/^image\/(x-)?png$/i', $t ) ) )	
        {
            $msg .= "Error: $fname is not an image file.<br>\n";
            continue;
        }

        $n = $filelist[ 'name' ][$i];
        if ( ! preg_match( '/^(.*)[ _]+(.*)\.([a-z]+)$/i', $n, $parts ) ) {
            $msg .= "Error: $fname does not match name pattern.<br/>\n";
            continue;
        }

        $fn = $parts[1];
        $ln = $parts[2];
        $ext = $parts[3];
        
        $msg .= "Received photo $fname for $fn $ln.<br/>\n";
        $users = get_users(
            array(
                'meta_query' => array(
                    array(
                        'key' => 'first_name',
                        'value' => $fn,
                        'compare' => '=='
                    ),
                    array(
                        'key' => 'last_name',
                        'value' => $ln,
                        'compare' => '=='
                    )
                )
            )
        );
        if ( count( $users ) > 1 ) {
            $msg .= 'Found ' . count( $users ) . " WP users.<br/>\n";
            continue;
        }
        if ( count( $users ) == 0 ) {
            $msg .= "Could not find user $fn $ln.<br/>\n";
            continue;
        }
        $user_id = $users[0]->ID;
        
        $md5 = md5_file( $tmpName );
        $newFile = "$md5.$ext";
        $newLoc = "images/facebook/$newFile";

        $oldFile = $users[0]->get( 'facebook_image' );
        if ( strlen( $oldFile ) > 0 )
        {
            unlink( "images/facebook/$oldFile" );
            $msg .= "Deleted old file $oldFile<br/>\n";
        }
        
        if ( ! rename( $tmpName, $newLoc ) ) {
            $msg .= "Rename to $newLoc failed<br/>\n";
        } else {
            if ( ! chmod( $newLoc, 0644) )
                $msg .= "Chmod of $newLoc failed<br/>\n";
        } 

        update_user_meta( $user_id, 'facebook_image', $newFile );
        $msg .= "Set picture for $fn $ln to $newFile.<br/>\n";
    }
    
    return $msg;
}

function download_userlist( $userlist )
{
    date_default_timezone_set( "America/New_York" ); 

    $today = date( "Ymd" );
    $filename = "users_$today.csv";
    
    header( 'Content-Description: File Transfer', true, 200 );
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '";' );

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen( 'php://output', 'w' );

    $row = array( 'Firstname', 'Lastname', 'Email', 'Groups', 'School', 'Graduation_Year', 'Phone', 'Parents',
                  'Parent_Email', 'Emergency_Phone', 'Children', 'Address', 'City', 'State', 'Zipcode' );
    fputcsv( $f, $row );

    foreach ( $userlist as $user )
    {
        // Don't list users who have not been approved
        if ( ! $user->get( 'wp-approve-user' ) ) continue;
        if ( $user->user_login == 'attendance-pi' ) continue;

        $school =  $user->get( 'school' );
        if ( strtoupper($school) == 'NONE' ) $school = '';
        
        $row = array(
            htmlspecialchars_decode( $user->first_name ),
            htmlspecialchars_decode( $user->last_name ),
            $user->user_email,
            join( ', ', $user->get( 'team_role' ) ),
            $school,
            $user->get( 'graduation' ),
            $user->get( 'phone' ),
            $user->get( 'parents' ),
            $user->get( 'parent_email' ),
            $user->get( 'emergency_phone' ),
            $user->get( 'children' ),
            htmlspecialchars_decode( $user->get( 'address' ) ),
            $user->get( 'city' ),
            $user->get( 'state' ),
            $user->get( 'postalcode' )
        );
        fputcsv( $f, $row );
    }

    fclose( $f );
}

// Not normally needed!
function cleanFacebook( $doIt )
{
    $msg = "";

    $dir = "images/facebook";

    foreach ( scandir( $dir ) as $fn )
    {
        if ( ! preg_match( '/^[0-9a-f]+\.[a-z]+$/i', $fn ) )
        {
            $msg .= "$fn skipping<br/>\n";
            continue;
        }
        
        $users = get_users(
            array(
                'meta_query' => array(
                    array(
                        'key' => 'facebook_image',
                        'value' => $fn,
                        'compare' => '=='
                    )
                )
            )
        );
        if ( count( $users ) > 1 ) {
            $msg .= "$fn ERROR: Found " . count( $users ) . " WP users.<br>\n";
        }
        else if ( count( $users ) == 0 ) {
            if ( $doIt )
            {
                unlink( 'images/facebook/' . $fn );
                $msg .= "$fn DELETED<br/>\n";
            }
            else
                $msg .= "$fn TO DELETE<br/>\n";
        }
    }

    return $msg;
}
?>
