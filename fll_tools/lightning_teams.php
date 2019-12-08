<?php
require_once( "../include/fll_utils.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('../wp-backend/wp-blog-header.php');
http_response_code(200); // override wp

global $wpdb;
$q = $wpdb->prepare( "SELECT * FROM fll_scores where round=1 ORDER BY ID" );
$teamlist = $wpdb->get_results( $q, ARRAY_A );
?>

<!DOCTYPE html >

<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php if ( ! $isLoggedIn ) echo '<meta HTTP-EQUIV="refresh" CONTENT="120">' . "\n"; ?>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
        <title>Lightning Round Alliances</title>
        
    </head>
    <body>
        <div class="col-sm-10 col-sm-offset-1">
            <table class="table table-bordered">
                <tbody>
                    <?php
                    foreach ( $teamlist as $team )
                    {
                        echo '<tr>';
                        echo '  <td>' . esc_html( $team['NAME'] ) . '</td>';
                        echo '  <td>' . esc_html( $team['NAME'] ) . '</td>';
                        echo "</tr>\n";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
