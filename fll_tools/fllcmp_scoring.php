<?php
require_once( "../include/fll_utils.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('../wp-backend/wp-blog-header.php');
http_response_code(200); // override wp

$isLoggedIn = is_user_logged_in();

global $wpdb;

// Form submitted
if ( $isLoggedIn ) {
    if ( isset( $_FILES['upload'] ) )
    {
        // upload CSV of teams and create 1st round
        fllUploadTeams( $_FILES['upload'] );
    }
    if ( isset( $_POST[ 'updatescore' ] ) ) {
        $rowId = $_POST[ 'scoreid' ];
        if ( $rowId ) {
            $wpdb->update( 'fll_scores', array( 'NAME' => $_POST['name'], 'SCORE' => $_POST['score'] ), array( 'ID' => $rowId ) );
        }
    }
    if ( isset( $_POST[ 'fill_round' ] ) ) {
        $fillRnd = $_POST[ 'roundid' ];
        if ( $fillRnd ) {
            fllFillRound( $fillRnd );
        }
    }
}

?>

<!DOCTYPE html >

<html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php if ( ! $isLoggedIn ) echo '<meta HTTP-EQUIV="refresh" CONTENT="120">' . "\n"; ?>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
        <title>Teams Standings</title>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.Marquee/1.3.94/jquery.marquee.min.js"></script>

        <style>
         .marquee {
             overflow: hidden;
         }
         
         td
         {
             font: 20pt Arial, sans-serif;
             border: thin solid #000000;

         }
         tr.alt
         {
             background: #C0C0C0;
             border: thin solid #000000;
         }
         td.alt
         {
             background: #C0C0C0;
             border: thin solid #000000;
         }

         .bracket {
             display: flex;
             flex-direction: row;
             font: 20pt Arial, sans-serif;
         }

         .bracket ul {
             display: flex;
             flex-direction:column;
             width: 250px;
             list-style:none;
             padding:0;
         }

         /* order matters here */
         .game + li {
             flex-grow:1;
         }
         .bracket li:first-child, .bracket li:last-child {
             flex-grow:.5;
         }

         .game {
             padding-left:20px;
         }


         .winner {
             font-weight:bold;
         }

         .game span {
             float:right;
             margin-right:5px;
         }

         .bracket-line-under {
             border-bottom:2px solid black;
         }

         .bracket-line-vertical {
             border-right:2px solid black;
             min-height:40px;
         }

         .bracket-line-over {
             border-top:2px solid black;
         }     
        </style>
    </head>
    <body>
        <?php
        if ( $isLoggedIn ) {
            fllScorePage();
        } else {
            fllDisplayScores();
        }
        ?>
    </body>
</html>
