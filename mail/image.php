<?php
define('WP_USE_THEMES', false);
require_once('../wp-backend/wp-blog-header.php');
http_response_code(200);

$uri = $_SERVER['REQUEST_URI'];
$uri = explode("/", $uri);
$page = $uri[sizeof($uri) - 1];
$tracking_id = str_replace(".gif", "", $page);

$q = $wpdb->prepare("UPDATE `email-tracking` SET `open-time`=UNIX_TIMESTAMP() WHERE `id`=%s AND `open-time`=0", $tracking_id);
$wpdb->query($q);

header("Content-Type: image/gif");
// 1 pixel transparent gif
echo base64_decode("R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");