<?php
require_once( 'include/page_elements.php' );

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
      <div class="col no-side-padding">

        <?php 
        output_header(); 
        output_navbar();
        ?>

        <div class="row page-body">
          <div class="col-md-12 offset-md-0 col-sm-10 offset-sm-1 col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row bottom-margin">
              <div class="col">
                <center><div class="notindex-title">CURRENT SPONSORS</div></center>

                <div class="col-sm-10 offset-sm-1 col-xs-12">
                  <div class="row spr-first-section">
                    <div class="col big-sprs">
                      <embed class="sprs-image" src="/images/sponsor-logos/sponsor_page_full_2020.svg" />
                    </div>
                    <div class="col small-sprs text-margins">
                      <embed class="sprs-image" src="/images/sponsor-logos/sponsor_page_narrow_2020.svg" />
                    </div>
                <div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
