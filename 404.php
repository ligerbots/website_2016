<?php
    require_once( "include/page_elements.php" );
    require_once( "include/utils.php" );
    http_response_code(404);
?>

<!DOCTYPE html>
<html>
  <?php page_head( "Page not found" ); ?>

  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid px-0">
      <div class="col-xs-12 px-0">

        <?php 
            output_header(); 
            output_navbar();
        ?>

        <div class="row page-body">
          <div class="col-md-12 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row side-margins row-margins bottom-margin">
                <center><div class="notindex-title" style="margin-bottom: 3rem;">PAGE NOT FOUND</div></center>
                <p><center>
		    <img src="/images/dozer_uh_oh.png" style="max-width: 100%;" title="Dozer Uh Oh" /><br />
                    The requested page does not exist.<br/>
                    <a href="/contact">Contact us</a><br/>
                    <a href="/">Return to home page</a>
                </center></p>

            </div>

            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
  </body>
</html>
