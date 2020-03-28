<?php
require_once( 'include/page_elements.php' );
require_once( 'include/sponsor_utils.php' );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once( 'wp-backend/wp-blog-header.php' );
http_response_code(200); // override wp
?>

<!DOCTYPE html>
<html>
  <?php
  page_head( "Current Sponsors" ); 
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
              <center><div class="notindex-title">CURRENT SPONSORS</div></center>

              <div class="col-sm-10 col-sm-offset-1 col-xs-12">
                <div class="row spr-first-section">
                  <div class="big-sprs">
                    <embed class="sprs-image" src="/images/sponsor-logos/sponsor_page_full_2020.svg" />
                  </div>
                  <div class="small-sprs text-margins">
                    <embed class="sprs-image" src="/images/sponsor-logos/sponsor_page_narrow_2020.svg" />
                  </div>
                <div>
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
