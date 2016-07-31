<?php

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

function get_latest_blog()
{
    /* TODO filter on type */
    $args = array( 'numberposts' => '1' );
	$recent_posts = wp_get_recent_posts( $args );
    return get_post( $recent_posts[0][ 'ID' ] );
}

function find_first_image( $post )
{
    $html = $post->post_content;
    /*preg_match( '/<img[^>]+>/i', $html, $result );*/

    preg_match( '/<img[^>]+src=[\'"](?P<src>.+?)[\'"][^>]*>/i', $html, $result );
    return '<img src="' . $result['src'] . '">';
}

?>
