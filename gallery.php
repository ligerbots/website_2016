<?php
require_once( "../private/wp-secrets.php" );
require_once("include/gallery-utils.php");
$flickr = createFlickr();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://ligerbots.org/images/liger.ico"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Serif:700" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="https://ligerbots.org/css/ligerbots.css" />

    <title>Ligerbots Gallery</title>
    <style>
     .title-bar {
         margin-bottom: 34.5pt;
         text-align: center;
     }
     .link {
         font-weight: bold !important;
         font-style: italic !important;
         padding: 0;
     }
     .gallery {
         padding: 0 26.79pt;
     }
     .year-container {
         margin: 0;
     }
     .year-number {
         margin-top: 46pt;
         margin-bottom: 26pt;
         color: #0066B3;
     }
     .thumbnail-container {
         
     }
     .thumbnail {
         padding: 0;
         border-radius: 0;
         margin: 7.71pt;
         border: 2pt solid #0066B3;
         display: inline-block;
         width: 189.75pt;
         height: 189.75pt;
         position: relative;
         transition: 0.3s;
     }
     .thumbnail:hover {
         opacity: 0.7;
     }
     .caption {
         z-index: 1;
         background: #0066B3;
         width: 100%;
         position: absolute;
         bottom: 0;
         text-align: center;
         padding: 0 1em;
         color: #FFFFFF !important;
     }
     .popup-container {
         display: none;
         position: fixed;
         z-index: 1;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         overflow: auto;
         padding-top: 250px;
     }
     .close-popup {
         position: absolute;
         top: 10%;
         right: 10%;
         font-size: 35px;
         font-weight: bold;
         color: #FFFFFF;
         transition: 0.3s;
     }
     .close-popup:hover {
         cursor: pointer;
         color: #CCCCCC;
     }
     .popup-image {
         display: block;
         width: 70%;
     }
    </style>
  </head>

  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-xs-12 no-side-padding">
        <div class="row header">
          <div class="masthead">
            <a href="/"><img id="liger-text" src="https://ligerbots.org/images/masthead_text.svg"/><img id="liger-head" src="https://ligerbots.org/images/liger_head.svg"/></a>
          </div>
          <ul>
            <li>
              <a class="header-link" target="_blank" href="http://www.firstinspires.org/robotics/frc">
                <img src="https://ligerbots.org/images/first.svg"/>
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA">
                <img src="https://ligerbots.org/images/youtube.png"/>
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://twitter.com/ligerbots">
                <img src="https://ligerbots.org/images/twitter.png">
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.facebook.com/The-LigerBots-162121450506644/">
                <img src="https://ligerbots.org/images/facebook.png">
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.instagram.com/ligerbots_frc2877/">
                <img src="https://ligerbots.org/images/instagram.png">
              </a>
            </li>
            <li>
              <a class="header-link" target="_blank" href="https://www.flickr.com/photos/ligerbots/">
                <img src="https://ligerbots.org/images/flickr.png">
              </a>
            </li>
            <li>
              <a class="header-link" href="/sponsor">
                <img style="width:10%" src="https://ligerbots.org/images/donate.png">
              </a>
            </li>
          </ul>
        </div>
        <nav class="navbar navbar-ligerbots">
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
                <li>
                  <a href="/about">About</a>
                </li> 
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Support<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/sponsor">Become a Sponsor</a>
                    </li>
                    <li>
                      <a href="/current-sponsors">Current Sponsors</a>
                    </li>
                  </ul>
                </li>
                <li>
                  <a href="/contact">Contact</a>
                </li> 
                <li>
                  <a href="/calendar.php">Calendar</a>
                </li> 
                <li>
                  <a href="/fll">FLL</a>
                </li> 
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Gallery<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/gallery2016">2016</a>
                    </li>
                  </ul>
                </li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">Resources<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/carpools.php">Carpools</a>
                    </li>
                    <li>
                      <a href="/links">Team Links</a>
                    </li>
                    <li>
                      <a href="/directory.php">Directory</a>
                    </li>
                    <li>
                      <a href="/facebook.php">Facebook</a>
                    </li>
                  </ul>
                </li>
                <li class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">My Account<span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/attendance.php">My Attendance</a>
                    </li>
                    <li>
                      <a href="/wp-backend/wp-admin/edit.php">Edit Posts</a>
                    </li>
                    <li>
                      <a href="/mail.php">Email Tracking</a>
                    </li>
                    <li>
                      <a href="/wp-backend/wp-admin/profile.php">My Profile</a>
                    </li>
                    <li>
                      <a href="/login.php?logout">Logout</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </nav>

        <div class="row page-body">
          <div class="col-md-12 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row bottom-margin text-background">

              <div class="title-bar">
                <div class="notindex-title">PHOTOS</div>
              </div>
              <center>
                <h5 class="link">LigerBots Flickr:
                  <a href="https://www.flickr.com/photos/ligerbots/">flickr.com/photos/ligerbots/</a>
                </h5>
                <h5 class="link">LigerBots Videos:
                  <a href="https://www.youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA">youtube.com/channel/UCgNgdmtDs7d58dVR-80DCGA</a>
                </h5>
              </center>

              <div class=" gallery container-fluid">
                <div class="row year-container">
                  <?php
                  //the url has no album display specification; show the year view
                  if ( ! isset( $_GET["album"] ) ) {
                      $albumList = getAlbumTree($flickr);

                      $count = 0;
                      foreach ($albumList as $year) {
                          echo "<div class=\"row year-container\">\n";
                          //the year header
                          echo "  <center><h4 class=\"year-number\"><b>".$year["title"]."</b></h4></center>\n";
                          //create each album box
                          foreach ($year["set"] as $album) {
                              $albumPhotoUrl = getPrimaryPhoto($flickr, $album["id"]);
                              echo "<a href=\"gallery.php?album=".$album["id"]."\">\n";
                              echo "  <div class=\"thumbnail\" style=\"background: url($albumPhotoUrl) 50% 50% no-repeat; background-size: cover;\">\n";
                              echo "    <div class=\"caption\">\n";
                              echo      $album["title"];
                              echo "    </div>\n";
                              echo "  </div>\n";
                              echo "</a>\n";
                              $count += 1;
                              if ( $count > 6 ) break;
                          }
                          echo "</div>\n";
                          break;
                      }
                  } else {
                      // there is an album to display; show as a list of photos
                      // a list of urls for each photo in the album
                      $photoInfoList = getPhotoList( $flickr, $_GET["album"] );
                      foreach ( $photoInfoList as $photoInfo ) {
                          echo "<a data-fancybox=\"gallery\" href=\"" . $photoInfo[ "large" ] . "\">\n";
                          echo "  <div class=\"thumbnail\" style=\"background: url(" . $photoInfo["small"] . ") 50% 50% no-repeat; background-size: cover;\"></div>\n";
                          echo "</a>\n";
                      }
                  }
                  ?>
                </div>
              </div>

            </div>
            <div class="row row-margins">
              <div class="col-xs-12">
                <div class="panel panel-sprs">
                  <div class="row spr-row">
                    <div class="spr-column">
                      <a href="http://www.ptc.com/company/community/first/">
                        <img src="https://ligerbots.org/images/sponsor-logos/ptc.png" style="margin-top:10px;" />
                      </a>
                      <br/>
                      <a href="http://www.dunkindonuts.com/dunkindonuts/en.html">
                        <img src="https://ligerbots.org/images/sponsor-logos/dunkin.svg" />
                      </a>
                    </div>
                    <div class="spr-column">
                      <div class="vcenter">
                        <a href="http://www.village-bank.com/">
                          <img src="https://ligerbots.org/images/sponsor-logos/VillageBank.svg" style="width:130px; margin-top:20px;" />
                        </a>
                      </div>
                    </div>
                    <div class="spr-column">
                      <a href="http://www.mcvittiefinancial.com/">
                        <img src="https://ligerbots.org/images/sponsor-logos/McVittieTaxAdvisors.svg" style="width:175px; margin-top:10px; margin-bottom:5px;" />
                      </a>
                      <br/>
                      <img src="https://ligerbots.org/images/sponsor-logos/atonce.svg" style="width:135px; margin-top:10px; margin-left:20px;" />
                    </div>
                    <div class="spr-column">
                      <a href="http://www.google.com/">
                        <img src="https://ligerbots.org/images/sponsor-logos/google-color.svg" style="width:165px; margin-top:5px;" />
                      </a>
                      <br/>
                      <a href="http://www.raytheon.com/">
                        <img src="https://ligerbots.org/images/sponsor-logos/raytheon.svg" style="width:165px; margin-top:7px;" />
                      </a>
                    </div>
                    <div class="spr-column">
                      <a href="http://www.wholefoodsmarket.com/">
                        <img src="https://ligerbots.org/images/sponsor-logos/WholeFoods.svg" style="width:100px; margin-top:5px;" />
                      </a>
                    </div>
                    <div class="spr-column">
                      <a href="http://www.newtonschoolsfoundation.org/">
                        <img src="https://ligerbots.org/images/sponsor-logos/nsf.svg" />
                      </a>
                    </div>
                    <div class="spr-column">
                      <a href="http://www.newton.k12.ma.us/">
                        <img src="https://ligerbots.org/images/sponsor-logos/nps.svg" style="width:145px;" />
                      </a>
                      <br/>
                      <a href="http://www.sharkninja.com/">
                        <img src="https://ligerbots.org/images/sponsor-logos/SharkNinja_Corporate.svg" style="width:145px; margin-top:7px;" />
                      </a>
                    </div>
                  </div>
                </div>
                <div style="text-align: center;">
                  <p class="label-orange">
                    <a href="/current-sponsors">Thank you to ALL our Sponsors (click here)!</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- add fancybox image zooming scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.0.47/jquery.fancybox.min.js"></script>
    <script type="text/javascript">
     //$(document).ready(
     //    function() {
     //        $(".fancybox").fancybox();
     //    }
     //);
     $.fancybox.defaults.slideShow = false;
     $.fancybox.defaults.fullScreen = false;
    </script>
  </body>
</html>
