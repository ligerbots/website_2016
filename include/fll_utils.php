<?php

/* Short and sweet */
//define('WP_USE_THEMES', false);
//require_once('wp-backend/wp-blog-header.php');

function fllUploadTeams( $filelist )
{
    global $wpdb;

    if ($filelist["size"] == 0) return "File is empty";

    $fileName = $filelist["tmp_name"];
    $file = fopen($fileName, "r");
    
    $teams = array();
    while ( ($column = fgetcsv($file, 1000, ",")) !== FALSE )
    {
        if ( $column[0] == "Team" || $column[0] == "TeamNumber" ) continue;
        array_push( $teams, intval($column[0]) );
    }

    $nteams = count($teams);
    for ( $i=0; $i < $nteams/2; $i++ ) {
        $t2 = $teams[$nteams - $i - 1];
        if ( $t2 < $teams[$i] ) {
            $name = $t2 . " / " . $teams[$i];
        } else {
            $name = $teams[$i] . " / " . $t2;
        }            
        $wpdb->update( 'fll_scores', array( 'NAME' => $name, 'SCORE' => '' ), array( 'ROUND' => 1, 'MATCH' => $i+1 ) );
    }
    // clear the later rounds
    for ($i = 2; $i <= 5; $i++) {
        $wpdb->update( 'fll_scores', array( 'NAME' => '', 'SCORE' => '' ), array( 'ROUND' => $i ) );
    }
    
    return '';
}

function fllScorePage()
{
    global $wpdb;

    $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=-1" );
    $scores = $wpdb->get_results( $q, ARRAY_A );
    $activeRound = $scores[0]['SCORE'];
    
    echo "<div class='container'>\n";

    if ( $activeRound < 0 ) {
        echo '<br><form class="form-inline" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
        echo '<div class="form-group">';
        echo '<label for="upload">Select CSV team list, ordered by rank:</label>';
        echo '<input class="form-control" type="file" id="upload" name="upload">';
        echo "</div>\n";
        echo '<button name="submit" type="submit" class="btn btn-default">Submit</button>';
        echo "</form>\n";
    }
    
    echo "<h1>Team Scores</h1>
    <div class='col-sm-12'>'\n";
    
    $q = $wpdb->prepare( "SELECT * FROM fll_scores ORDER BY `round`, `match`, `id`" );
    $rowList = $wpdb->get_results( $q, ARRAY_A );
    $rr = -1;
    foreach ( $rowList as $row )
    {
        $round = $row['ROUND'];
        if ( $activeRound > 1 && $round == 1 ) continue;
        
        if ( $rr != $round ) {
            if ($round > 1 && $round <= 5) {
                echo '<form class="form-inline" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                echo "<input type='hidden' name='roundid' value='" . $round . "'>\n";
                echo "<button type='submit' name='fill_round' class='btn btn-default'>Fill Round " . $round . "</button>\n";
                echo "</form>\n";
            }
            echo "<br/>\n";
        }
        $rr = $round;

        echo "<div class='row'>\n";
        echo "<form class='form-inline' method='post' action='" . $_SERVER['PHP_SELF'] . "'>\n";
        echo "<div class='form-group col-sm-2'>\n";
        echo "<input type='hidden' name='scoreid' value='" . $row['ID'] . "'>\n";
        echo "<label>Round " . $round . " Match " . $row['MATCH'] . "</label>\n";
        echo "</div>
        <label> Name:</label>
        <div class='input-group col-sm-4'>\n";
        echo "<input type='text' class='form-control' id='name' name='name' value='" . $row['NAME'] . "'>\n";
        echo "</div>
        <div class='form-group'>
        <label> Score:</label>\n";
        echo "<input type='number' class='form-control' id='score' name='score' width='30' value='" . $row['SCORE'] . "'>\n";
        echo "</div>
        <div class='form-group'>
        <button type='submit' name='updatescore' class='btn btn-default'>Update</button>
        </div>
        </form>
        </div>\n";
    }
    echo "<br/></div>
        </div>\n";
}

function fllDisplayScores()
{
    global $wpdb;

    $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=-1" );
    $scores = $wpdb->get_results( $q, ARRAY_A );
    $activeRound = $scores[0]['SCORE'];
    
    if ( $activeRound == 0 ) {
        $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=0" );
        $scores = $wpdb->get_results( $q, ARRAY_A );
        $url = $scores[0]['NAME'];
        echo '<iframe src="' . $url . '" width="100%" height="1024px" frameborder="0" scrolling="no"></iframe>' . "\n";
    } elseif ( $activeRound == 1 ) {
        $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=1 ORDER BY score DESC, ID ASC" );
        $scores = $wpdb->get_results( $q, ARRAY_A );

        // Add function to scroll the scores
        echo <<<EOL
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
EOL;

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

        echo "</table>
        </div>
        </div>\n";
        
    } elseif ( $activeRound > 1 ) {
        $q = $wpdb->prepare( "SELECT * FROM fll_scores where round>1 order by `ROUND`, `MATCH`" );
        $scores = $wpdb->get_results( $q, ARRAY_A );

        echo "<div class='container'>
        <h1>Lightning Elimination Bracket</h1>
        <div class='bracket'>\n";

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

        echo "<li>&nbsp;</li>
        </ul>
        </div>
        </div>\n";
    }
}

function fllFillRound( $round )
{
    global $wpdb;

    if ( $round == 2 ) {
        $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=1 order by `SCORE` desc" );
        $scores = $wpdb->get_results( $q, ARRAY_A );
        for ( $i=0; $i < 8; $i++ )
        {
            if ( $i < 4 )
                $slot = 2 * $i + 1;
            else
                $slot = 16 - 2 * $i;
            $wpdb->update( 'fll_scores', array( 'NAME' => ($i+1) . ". " . $scores[$i]['NAME'], 'SCORE' => '' ), array( 'ROUND' => 2, 'MATCH' => $slot ) );
        }
    }
    else if ( $round > 2 ) {
        $q = $wpdb->prepare( "SELECT * FROM fll_scores where round=" . ($round - 1) . " order by `MATCH`" );
        $scores = $wpdb->get_results( $q, ARRAY_A );

        $n = count($scores);
        for ( $i=0; $i < $n; $i += 2 )
        {
            $slot = $i / 2 + 1;
            $winner = $i;
            if ( $scores[$i+1]['SCORE'] > $scores[$i]['SCORE'] ) $winner = $i+1;
            $wpdb->update( 'fll_scores', array( 'NAME' => $scores[$winner]['NAME'], 'SCORE' => '' ), array( 'ROUND' => $round, 'MATCH' => $slot ) );
        }
    }
}

