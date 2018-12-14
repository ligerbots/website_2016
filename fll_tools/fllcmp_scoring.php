<?php
require_once( "../include/page_elements.php" );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('../wp-backend/wp-blog-header.php');
http_response_code(200); // override wp

$isLoggedIn = is_user_logged_in();

global $wpdb;

// Form submitted
if ( isset( $_POST[ 'updatescore' ] ) && $isLoggedIn ) {
    $rowId = $_POST[ 'scoreid' ];
    if ( $rowId ) {
        $wpdb->update( 'fll_scores', array( 'NAME' => $_POST['name'], 'SCORE' => $_POST['score'] ), array( 'ID' => $rowId ) );
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
      if ( $isLoggedIn ):
        $q = $wpdb->prepare( "SELECT * FROM fll_scores ORDER BY `round`, `match`, `id`" );
        $rowList = $wpdb->get_results( $q, ARRAY_A );
      ?>
        <div class="container">
          <h1>Team Scores</h1>
          <div class="col-sm-12">
            <?php
            $rr = -1;
            foreach ( $rowList as $row ):
                  if ( $rr != $row['ROUND'] ) echo "<br/>\n";
                $rr = $row['ROUND'];
            ?>
              <div class='row'>
                <form class="form-inline" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                  <div class="form-group col-sm-2">
                    <input type="hidden" name="scoreid" value="<?php echo $row['ID']; ?>">
                    <label>Round <?php echo $row['ROUND'] . ' Match ' . $row['MATCH']; ?></label>
                  </div>
                  <label> Name:</label>
                  <div class="input-group col-sm-4">
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['NAME']; ?>">
                  </div>
                  <div class="form-group">
                    <label> Score:</label>
                    <input type="number" class="form-control" id="score" name="score" width="30" value="<?php echo $row['SCORE']; ?>">
                  </div>
                  <div class="form-group">
                    <button type="submit" name="updatescore" class="btn btn-default">Update</button>
                  </div>
                </form>
              </div>
            <?php endforeach;?>
          </div>
        </div>
      <?php
      else:
      $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=-1" );
      $scores = $wpdb->get_results( $q, ARRAY_A );
      $roundNo = $scores[0]['SCORE'];
      if ( $roundNo == 0 ) {
           $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=0" );
           $scores = $wpdb->get_results( $q, ARRAY_A );
           $url = $scores[0]['NAME'];
           echo '<iframe src="' . $url . '" width="100%" height="1024px" frameborder="0" scrolling="no"></iframe>' . "\n";
      } elseif ( $roundNo == 1 ) {
          $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=1 ORDER BY score DESC, ID ASC" );
          $scores = $wpdb->get_results( $q, ARRAY_A );
      ?>
          <script>
           $(document).ready(function() {
               $('.marquee').marquee({
                   //speed in milliseconds of the marquee
                   duration: 20000,
                   //gap in pixels between the tickers
                   gap: 25,
                   //time in milliseconds before the marquee will start animating
                   delayBeforeStart: 1000,
                   //'left' or 'right'
                   direction: 'up',
                   //true or false - should the marquee be duplicated to show an effect of continues flow
                   duplicated: true,
                   startVisible: true
               });
               setInterval( "reloadPage()", 120000 );
           });
           function reloadPage()
           {
	       window.location = window.location;
	       setInterval( "reloadPage()", 120000 );
           }
          </script>
          <div class="container">
            <h1>Lightning Round 1 Standings</h1>
            <div class="marquee col-sm-12">
              <table class="table table-condensed table-striped">
	        <tr>
                  <td width="30px">Rank</td>
	          <td width="200px">Team</td>
	          <td width="30px">Score</td>
	        </tr>
                <?php
                $i = 0;
                foreach ( $scores as $row )
                {
                    $i++;
                    echo '<tr>';
                    echo "  <td>$i</td>\n";
                    echo '  <td>' . esc_html( $row['NAME'] ) . "</td>\n";
                    echo '  <td>' . esc_html( $row['SCORE'] ) . "</td>\n";
                    echo "</tr>\n";
                }
                ?>
              </table>
            </div>
          </div>
      <?php
      } elseif ( $roundNo > 1 ) {
          $q = $wpdb->prepare( "SELECT * FROM fll_scores where round>1 order by `ROUND`, `MATCH`" );
          $scores = $wpdb->get_results( $q, ARRAY_A );
      ?>
            <div class="container">
            <h1>Lightning Elimination Bracket</h1>
            <div class="bracket">
              <?php
              $rnd = 1;
              $ulOpen = FALSE;
              $firstTeam = TRUE;
              foreach ( $scores as $row ) {
                  if ( $rnd != $row['ROUND'] ) {
                      if ( $ulOpen ) echo "<li>&nbsp;</li>\n</ul>\n";
                      echo "<ul>\n";
                      $ulOpen = TRUE;
                      $rnd = $row['ROUND'];
                  }
                  if ( $firstTeam ) {
                      echo "<li>&nbsp;</li>\n";
                      echo '<li class="game bracket-line-under">';
                  } else {
                      echo '<li class="bracket-line-vertical">&nbsp;</li>' . "\n";
                      echo '<li class="game bracket-line-over">';
                  }
                  if ( strlen( $row['NAME']) > 0 ) 
                      echo $row['NAME'] . '<span>' . $row['SCORE'] . "</span></li>\n";
                  else
                      echo "&nbsp;<span>&nbsp;</span></li>\n";
                  
                  $firstTeam = ! $firstTeam;
              }
              ?>
              <li>&nbsp;</li>
              </ul>
            </div>
          </div>
      <?php
      }
      endif;
      ?>

  </body>
</html>
