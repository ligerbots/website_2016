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

?>
