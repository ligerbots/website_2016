<?php
   require_once( "include/page_elements.php" );

   function php_split_js_make_email( $email, $text )
   {
     $pieces = explode("@", $email);
   
     echo '<script type="text/javascript">';
     echo 'var b = "' . $pieces[0] . '";';
     echo 'var a = "<a href=\'mailto:";';
     echo 'var c = "' . $pieces[1] .'";';
     echo 'var d = "\' class=\'email\'>";';
     echo 'var e = "</a>";';
     echo 'var t = "' . $text . '";'; 
     echo 'document.write(a+b+"@"+c+d+t);';
     echo '</script><noscript>Please enable JavaScript to view emails</noscript>';
   }
?>

<!DOCTYPE html>
<html>
  <?php page_head( "Contact the LigerBots" ); ?>

  <body>
    <div class="container-fluid no-side-padding">
      <div class="col-lg-10 col-lg-offset-1 col-md-12">

        <?php 
           output_header(); 
           output_navbar();
           ?>
        
        <div class="page-body">
          <div class="row side-margins bottom-margin">

            <center>
	      <h1>Contact the LigerBots</h1><br>
	      <h4>
                <?php php_split_js_make_email( "execs@ligerbots.com", "Email the executive board" ); ?>
              </h4>
	      <h4>
                <?php php_split_js_make_email( "coaches@ligerbots.com", "Email the coaches" ); ?>
              </h4>
            </center>

          </div>

          <?php output_footer(); ?>
        
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
  </body>
</html>
