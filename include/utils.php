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
    $msg .= "Please wait for the approval email before trying to log in.\n\n";
    $msg .= "LigerBots Administrator\nweb@ligerbots.org\n";
    wp_mail( $email, 'LigerBots Registration', $msg );
    
    return NULL;
}

?>
