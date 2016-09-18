<?php

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

/* Call the WP setup_postdata function, but also set global $post variable. */
/*   This allows the "loop" functions to work. */
function my_setup_postdata( $postId )
{
    global $post;

    $post = get_post( $postId );
    setup_postdata( $postId );
}

$lg_keepReadMore = FALSE;
function custom_excerpt_more( $more ) {
    global $lg_keepReadMore;
    if ( $lg_keepReadMore ) return $more;
	return '...';
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );
function custom_excerpt_length( $length ) {
	return 35;
}
add_filter( 'excerpt_length', 'custom_excerpt_length' );
function my_the_excerpt( $keepReadMore )
{
    global $lg_keepReadMore;
    $lg_keepReadMore = $keepReadMore;
    the_excerpt();
}

function print_filters_for( $hook = '' ) {
    global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
        return;

    print '<pre>';
    print_r( $wp_filter[$hook] );
    print '</pre>';
}
//print_filters_for( 'the_content_more_link' );

function get_latest_blog()
{
    $cat = get_category_by_slug( 'blog' );
    $args = array( 'numberposts' => '1',
                   'post_type' => 'post',
                   'post_status' => 'publish',
                   'category' => $cat->term_id );
    $recent_posts = wp_get_recent_posts( $args );
    return get_post( $recent_posts[0][ 'ID' ] );
}

function find_first_image( $myPost )
{
    $html = $myPost->post_content;
    /*preg_match( '/<img[^>]+>/i', $html, $result );*/

    preg_match( '/<img[^>]+src=[\'"](?P<src>.+?)[\'"][^>]*>/i', $html, $result );
    return '<img src="' . $result['src'] . '">';
}

function get_announcements( $count )
{
    $cat = get_category_by_slug( 'announcements' );
    $args = array( 'numberposts' => $count,
                   'post_type' => 'post',
                   'post_status' => 'publish',
                   'category' => $cat->term_id );
    $res = array();
    foreach ( wp_get_recent_posts( $args ) as $p )
    {
        array_push( $res, $p[ 'ID' ] );
    }
    return $res;
}

//--------------------------------------------------------------------------------
// Carpool Routines

// WARNING: table names seem to be case-sensitive; do not know why!!
function fetch_carpools()
{
    global $wpdb;

    // Formulate the query
    // If you want to limit to 5, use the MySQL "LIMIT" operand
    $q = $wpdb->prepare( "SELECT * FROM carpools ORDER BY id" );
    
    // Execute the query, return results as an array of dictionaries
    $var = $wpdb->get_results( $q, ARRAY_A );
    //print 'func_call: ' . $wpdb->func_call . "<br>\n";
    //print 'last_error: ' . $wpdb->last_error . "<br>\n";
    
    return $var;
}

function add_carpool( $label )
{
    global $wpdb;
    $wpdb->insert( 'carpools', array( 'LABEL' => $label ) );
}

function delete_carpool( $id )
{
    global $wpdb;
    $wpdb->delete( 'carpools', array( 'ID' => $id ) );
}

//--------------------------------------------------------------------------------
// Registration routines

function register( $post )
{
    // First, some validation and formatting
    $username = $post[ 'username' ];
    if ( empty( $username ) )
        return "No username specified";
    $user = get_user_by( 'login', $username );
    if ( ! empty( $user ) )
        return "Username '$username' is already in use.";

    $email = $post[ 'email' ];
    $user = get_user_by( 'email', $email );
    if ( ! empty( $user ) )
        return "Email '$email' is already registered.";
    
    $password = $post[ 'password' ];
    $confirm = $post[ 'password-confirm' ];
    if ( $password != $confirm )
        return 'Password and confirmation do not agree';
    if ( strlen( $password ) < 8 )
        return 'Password is too short';
    
    $userdata = array(
        'user_login' => $username,
        'user_pass' => $password,
        'user_email' => $email,
        'first_name' => $post[ 'first-name' ],
        'last_name' => $post[ 'last-name' ]
    );
    
    $user_id = wp_insert_user( $userdata );

    // On success
    if ( is_wp_error( $user_id ) ) {
        $error_msg = $user_id->get_error_message();
        return "Error creating user: $error_msg";
    }

    // Set new user as un-approved
    // Ignore "error" since it may already be set to false
    update_user_meta( $user_id, 'wp-approve-user', false );
    
    $extraProp = array(
        'phone', 'address', 'city', 'state', 'postalcode', 'emergency_phone',
        'school', 'graduation', 'parent_email'
    );

    // team_role, children, parent_email, parents
    foreach ( $extraProp as $prop )
    {
        $val = $post[ $prop ];
        if ( $val )
        {
            if ( ! update_user_meta( $user_id, $prop, $val ) )
                return "Error setting property '$prop'";
        }
    }

    $role = array();
    if ( $post[ 'user-type' ] == 'student' )
    {
        array_push( $role, 'Student' );
        if ( $post[ 'role-exec' ] == 'on' )
            array_push( $role, 'Exec' );
    }
    else
    {
        if ( $post[ 'role-parent' ] == 'on' )
            array_push( $role, 'Parent' );
        if ( $post[ 'role-coach' ] == 'on' )
            array_push( $role, 'Coach' );
        if ( $post[ 'role-mentor' ] == 'on' )
            array_push( $role, 'Mentor' );
    }
    if ( ! update_user_meta( $user_id, 'team_role', $role ) )
        return "Error setting property 'team_role'";
        
    $l = count( $post[ 'child-first-name' ] );
    if ( $l > 0 )
    {
        $children = array();
        for ( $i=0; $i<$l; $i++ )
        {
            if ( $post[ 'child-first-name' ][ $i ] || $post[ 'child-last-name' ][ $i ] )
                array_push( $children, $post[ 'child-first-name' ][ $i ] . ' ' . $post[ 'child-last-name' ][ $i ] );
        }
        if ( count( $children ) > 0 )
        {
            if ( ! update_user_meta( $user_id, 'children', implode( ',', $children ) ) )
                return "Error setting property 'children'";
        }
    }

    $l = count( $post[ 'parent-first-name' ] );
    if ( $l > 0 )
    {
        $parents = array();
        for ( $i=0; $i<$l; $i++ )
        {
            if ( $post[ 'parent-first-name' ][ $i ] || $post[ 'parent-last-name' ][ $i ] )
                array_push( $parents, $post[ 'parent-first-name' ][ $i ] . ' ' . $post[ 'parent-last-name' ][ $i ] );
        }
        if ( count( $parents ) > 0 ) 
        {
            if ( ! update_user_meta( $user_id, 'parents', implode( ',', $parents ) ) )
                return "Error setting property 'parents'";
        }
    }

    // Email the admin and user
    // Note: don't use wp_send_new_user_notifications, at least for the user message.
    //   It will reset the password, and does not send a nice text.
    
    $msg = "New user '$username' '$email' has register with the LigerBots website\n";
    wp_mail( get_option( 'admin_email' ), 'LigerBots New User Registration', $msg );

    $msg = "Thank you for registering as '$username' with the LigerBots.\n";
    $msg .= "The administrator has been notified.\n\n";
    $msg .= "You can log in once your account has been approved.\n\n";
    $msg .= "LigerBots Administrator web@ligerbots.org\n";
    wp_mail( $email, 'LigerBots Registration', $msg );
    
    return NULL;
}

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
            $msg .= "Error: $fname is not an uploaded file.<br>\n";
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
            $msg .= "Error: $fname does not match name pattern.<br>\n";
            continue;
        }

        $fn = $parts[1];
        $ln = $parts[2];
        $ext = $parts[3];
        
        $msg .= "Received photo $fname for $fn $ln.<br>\n";
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
            $msg .= 'Found ' . count( $users ) . " WP users.<br>\n";
            continue;
        }
        if ( count( $users ) == 0 ) {
            $msg .= "Could not find user $fn $ln.<br>\n";
            continue;
        }
        $user_id = $users[0]->ID;
        
        $md5 = md5_file( $tmpName );
        $newFile = "$md5.$ext";
        $newLoc = "images/facebook/$newFile";
        if ( ! rename( $tmpName, $newLoc ) ) {
            $msg .= "Rename to $newLoc failed<br>\n";
        } else {
            if ( ! chmod( $newLoc, 0644) )
                $msg .= "Chmod of $newLoc failed<br>\n";
        } 

        update_user_meta( $user_id, 'facebook_image', $newFile );
        $msg .= "Set picture for $fn $ln to $newFile.<br>\n";
    }
    
    return $msg;
}

/* TEMP remember this */
function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen('php://output', 'w');

    foreach ($array as $line) {
        fputcsv($f, $line, $delimiter);
    }
}

?>
