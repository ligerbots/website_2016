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

?>
