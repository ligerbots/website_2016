<?php
require_once( 'include/page_elements.php' );
require_once( 'include/sponsor_utils.php' );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once( 'wp-backend/wp-blog-header.php' );
http_response_code(200); // override wp

$icon_set_name = 'production';
$show_sponsor_bar = false;
// must be logged in for an alternate page
if ( is_user_logged_in() )
{
    if ( isset( $_GET[ 'icon_set' ] ) )
    {
        $icon_set_name = $_GET[ 'icon_set' ];
    }
    $show_sponsor_bar = isset( $_GET[ 'show_sponsor_bar' ] );
}

$logo_set = fetch_sponsor_info($icon_set_name);
$logo_set = set_pushpulls($logo_set);

$css = sponsor_page_css($logo_set);
?>

<!DOCTYPE html>
<html>
  <?php
  page_head( "Current Sponsors", false, NULL, $css ); 
  ?>
  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-xs-12 no-side-padding">

        <?php 
        output_header(); 
        output_navbar();
        ?>

        <div class="row page-body">
          <div class="col-md-12 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row bottom-margin">
              <div class="col-md-10 col-md-offset-1 col-sm-12">
                <center><div class="notindex-title">CURRENT SPONSORS</div></center>

                <div class="row spr-first-section">
                  <div class="spr-grey-rule"><div><img style="width:80px;" src="/images/sponsor-logos/puma_grey.svg"></div></div>
                  <center>Puma Level Sponsors</center>
                </div>
                <?php
                sponsor_logo_rows($logo_set['puma']);
                ?>

                <div class="row spr-section-heading">
                  <div class="spr-grey-rule"><div><img style="width:60px;" src="/images/sponsor-logos/panther_grey.svg"></div></div>
                  <center>Panther Level Sponsors</center>
                </div>
                <?php
                sponsor_logo_rows($logo_set['panther']);
                ?>

                <div class="row spr-section-heading">
                  <div class="spr-grey-rule"><div><img style="width:60px;" src="/images/sponsor-logos/cheetah_grey.svg"></div></div>
                  <center>Cheetah Level Sponsors</center>
                </div>
                <?php
                sponsor_logo_rows($logo_set['cheetah']);
                ?>
                
                <div class="row spr-text-section">
                  <div class="spr-grey-rule">
                    <div>    
                      <img style="width:40px; margin-right:25px;" src="/images/sponsor-logos/lynx_grey.svg">
                      <img style="width:40px; margin-left:25px; margin-right:25px;" src="/images/sponsor-logos/leopard_grey.svg">
                      <img style="width:40px; margin-left:25px;" src="/images/sponsor-logos/bobcat_grey.svg">
                    </div>
                  </div>
                  <center>Lynx Level Sponsors / Leopard and Bobcat Level Donors</center>
                </div>
                <?php
                sponsor_text_rows($logo_set['lynx']);
                ?>

              </div>
            </div>

            <?php
            if ($show_sponsor_bar) {
                echo '<div class="row top-spacer"> </div>' . "\n";
                echo '<div class="row row-margins">' . "\n";
                echo '  <div class="col-xs-12">' . "\n";
                echo '    <div class="panel panel-sprs">' . "\n";
                echo '      <div class="row spr-row">' . "\n";

                sponsor_bar_html($logo_set);
                echo "      </div>\n";
                echo "    </div>\n";
                echo '    <div style="text-align: center;">' ."\n";
                echo '      <p class="label-orange"><a href="/current-sponsors">Thank you to ALL our Sponsors (click here)!</a></p>' . "\n";
                echo "    </div>\n";
                echo "  </div>\n";
                echo "</div>\n";
            }
            ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
