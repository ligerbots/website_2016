<?php

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once(dirname(__FILE__) . '/../wp-backend/wp-blog-header.php');

function page_head( $title, $includeRSS=false, $extraCSS=NULL, $extraHTML="" )
{
    echo <<<EOL
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="/images/liger.ico"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
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

    if ( $includeRSS ) echo '    <link rel="alternate" type="application/rss+xml" href="/?feed=rss" title="LigerBots Blog Feed" />' . "\n";

    echo "    <title>$title</title>\n";

    /* Google Analytics tracking code */
    echo "    <script async src='https://www.googletagmanager.com/gtag/js?id=G-ZD4J3J1EZN'></script>\n";
    echo "    <script>\n";
    echo "       window.dataLayer = window.dataLayer || [];\n";
    echo "       function gtag(){dataLayer.push(arguments);}\n";
    echo "       gtag('js', new Date());\n";
    echo "       gtag('config', 'G-ZD4J3J1EZN');\n";
    echo "    </script>\n";

    echo "    <meta property=\"og:title\" content=\"$title\" />\n";
    echo $extraHTML;
    echo "\n  </head>\n";
}

function page_foot()
{
    echo <<<EOL
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script  type="text/javascript"src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
              <a class="header-link" target="_blank" href="https://www.youtube.com/c/ligerbots">
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
              <a class="header-link" href="/sponsor-us">
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
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">About<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/join">Join the LigerBots</a></li>
                    <li><a href="/about">About Us</a></li>
                    <li><a href="/contact">Contact Us</a></li> 
                  </ul>
                </li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Support<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/sponsor-us">Become a Sponsor</a></li>
                    <li><a href="/current-sponsors">Current Sponsors</a></li>
                  </ul>
                </li>
                <li><a href="/calendar.php">Calendar</a></li> 
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Outreach<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/outreach">Outreach</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="/fll">FLL</a></li>
                    <li><a href="/steam-expo">Exhibiting at the STEAM Expo</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="/educational-resources">Educational Resources</a></li>
                  </ul>
                </li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Media<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/gallery.php">Photos</a></li>
                  </ul>
                </li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Resources<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/carpools.php">Carpools</a></li>
                    <li><a href="/links">Team Links</a></li>';
    if ( $loggedIn ) {
        echo '<li><a href="/directory.php">Directory</a></li>';
        echo '<li><a href="/facebook.php">Facebook</a></li>';
        echo '<li><a href="/preseason-resources/">Preseason Resources</a></li>';
        echo '<li><a href="http://team.ligerbots.com">Team Internal Site</a></li>';
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
        <ul class="dropdown-menu">';
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

function output_footer( $sponsor_bar_name='home-sponsors' )
{
    echo <<<FOOTER
<div class="row row-margins">
  <div class="col-xs-12">
    <div class="panel panel-sprs">
      <div class="big-sprs">
        <embed class="sprs-image" src="/images/sponsor-logos/sponsor_bar_full_2025a.svg" />
      </div>
      <div class="small-sprs">
        <embed class="sprs-image" src="/images/sponsor-logos/sponsor_bar_narrow_2025a.svg" />
      </div>
    </div>
    <div style="text-align: center;">
      <p class="label-blue"><a href="/current-sponsors">Thank you to ALL our Sponsors (click here)!</a></p>
    </div>
  </div>
</div>
FOOTER;
}

?>
