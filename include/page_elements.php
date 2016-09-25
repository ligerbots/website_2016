<?php

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once('wp-backend/wp-blog-header.php');

function page_head( $title, $includeRSS=false, $extraCSS=NULL )
{
    echo <<<EOL
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/liger.ico"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Serif:700" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700" />
    <link rel="stylesheet" href="/css/ligerbots.css" />

EOL;

    if ( ! is_null( $extraCSS ) )
    {
        if ( is_string( $extraCSS ) ) 
            echo '    <link rel="stylesheet" href="' . $extraCSS . '" />' . "\n";
        elseif ( is_array( $extraCSS ) )
        {
            foreach ( $extraCSS as $c )
            {
                echo '    <link rel="stylesheet" href="' . $c . '" />' . "\n";
            }
        }
    }

    if ( $includeRSS ) echo '    <link rel="alternate" type="application/rss+xml" href="/?feed=rss" title="LigerBots Blog Feed">' . "\n";

    echo "    <title>$title</title>\n</head>\n";
}

function page_foot()
{
    echo <<<EOL
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
EOL;
}

function output_header()
{
    echo <<<EOL
        <div class="row header">
          <div class="masthead">
            <a href="/"><img id="liger-text" src="/images/masthead_text.svg"/><img id="liger-head" src="/images/liger_head.svg"/></a>
          </div>
          <ul>
            <li>
              <a class="header-link" target="_blank" href="http://www.firstinspires.org/robotics/frc">
                <img src="/images/first.svg"/>
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA">
                <img src="/images/youtube.png"/>
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://twitter.com/ligerbots">
                <img src="/images/twitter.png">
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.facebook.com/The-LigerBots-162121450506644/">
                <img src="/images/facebook.png">
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.instagram.com/ligerbots_frc2877/">
                <img src="/images/instagram.png">
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.flickr.com/photos/ligerbots/">
                <img src="/images/flickr.png">
              </a>
            </li>
            <li>
              <a class="header-link" href="/sponsor">
                <img style="width:10%" src="/images/donate.png">
              </a>
            </li>
          </ul>
        </div>
EOL;
}

function output_navbar()
{
    $loggedIn = is_user_logged_in();

    echo '<nav class="navbar navbar-ligerbots">
          <div class="container-fluid">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle navbar-toggle-ligerbots" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar icon-bar-ligerbots"></span>
                <span class="icon-bar icon-bar-ligerbots"></span>
                <span class="icon-bar icon-bar-ligerbots"></span> 
              </button>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
              <ul class="nav navbar-nav nav-stacked">
                <li class="active"><a href="/">Home</a></li>
                <li><a href="/about">About</a></li> 
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Support<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/sponsor">Become a Sponsor</a></li>
                    <li><a href="/current-sponsors">Current Sponsors</a></li>
                  </ul>
                </li>
                <li><a href="/contact">Contact</a></li> 
                <li><a href="/calendar.php">Calendar</a></li> 
                <li><a href="/fll">FLL</a></li> 
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Gallery<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/gallery2016">2016</a></li>
                  </ul>
                </li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Resources<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/carpools.php">Carpools</a></li>';
    if ( $loggedIn ) {
        echo '<li><a href="/directory.php">Directory</a></li>';
        echo '<li><a href="/facebook.php">Facebook</a></li>';
    }        
    echo "   </ul>\n";
    echo "   </li>\n";
    if ( ! $loggedIn ) 
    {
        // <li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
        echo '<li><a href="/login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>';
    }
    else
    {
        echo '<li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">My Account<span class="caret"></span></a>
        <ul class="dropdown-menu">
        <li><a href="/attendance.php">My Attendance</a></li>';
        if ( current_user_can( 'edit_posts' ) ) {
            echo '<li><a href="/wp-backend/wp-admin/edit.php">Edit Posts</a></li>';
        }
        echo '<li><a href="/wp-backend/wp-admin/profile.php">My Profile</a></li>
        <li><a href="/login.php?logout">Logout</a></li>
        </ul>
        </li>';
    }

    echo '</ul>';
    echo '</div> </div> </nav>';
}

function output_footer( $home_page=false )
{
    $page = get_page_by_path( 'home-sponsors' );

    echo '<div class="row row-margins">';
    echo '  <div class="col-xs-12">';
    echo '    <div class="panel panel-sprs">';
    // don't apply the filters. Raw html.
    echo $page->post_content;
    echo '    </div>';
    echo '    <div style="text-align: center;">';
    echo '      <p class="label-orange">Thank you to our sponsors!</p>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
}

?>
