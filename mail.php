<?php
// deal with wordpress escaping quotes automatically by saving the request array
// before wordpress gets a chance to mess with it
// this assignment copies the array
$_PRESANITIZE_REQUEST = $_REQUEST;

require_once( 'include/page_elements.php' );
require_once 'include/RandomStringGenerator.php';
$rnd = new RandomStringGenerator();

$hostname = $_SERVER['HTTP_HOST'];
$protocol = "https";
if(strpos($hostname, "dev2016") !== FALSE) {
  $protocol = "http";
}

if(!current_user_can('edit_posts')) {
  header("Location: /");
  die();
}
http_response_code(200); // no wordpress, this isn't a 404
date_default_timezone_set("America/New_York");

$action_message = "";
if($_REQUEST['action'] == "send") {
  $q = $wpdb->prepare("INSERT INTO `email-tracking-emails` (`subject`, `content`) VALUES(%s, %s)", $_PRESANITIZE_REQUEST['subject'], $_PRESANITIZE_REQUEST['content-html']);
  $wpdb->query($q);
  $id = $wpdb->get_results("SELECT LAST_INSERT_ID() FROM `email-tracking-emails`", ARRAY_A)[0]['LAST_INSERT_ID()'];
  
  for($i = 0; $i < sizeof($_PRESANITIZE_REQUEST['name-first']); $i++) {
    $tracking_id = $rnd->generate(32);
    $q = $wpdb->prepare(
      "INSERT INTO `email-tracking` (`id`, `email-id`, `name-first`, `name-last`, `email`) VALUES(%s, %s, %s, %s, %s)",
      $tracking_id,
      $id,
      $_PRESANITIZE_REQUEST['name-first'][$i],
      $_PRESANITIZE_REQUEST['name-last'][$i],
      $_PRESANITIZE_REQUEST['email'][$i]);
    $wpdb->query($q);
    
    $imgTag = "<img src='$protocol://$hostname/mail/$tracking_id.gif' />";
    $content = $_PRESANITIZE_REQUEST['content-html'];
    $content = str_replace('${name}', htmlspecialchars($_PRESANITIZE_REQUEST['name-first'][$i]), $content);
    $content .= $imgTag;
    
    $mail = new NonWpMailer\PHPMailer;

    //header("Content-Type: text/plain");
    //$mail->SMTPDebug = 3;
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $_PRESANITIZE_REQUEST['from-email'];
    $mail->Password = $_PRESANITIZE_REQUEST['smtp-password'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    $mail->setFrom($_PRESANITIZE_REQUEST['from-email'], $_PRESANITIZE_REQUEST['from-name']);
    $mail->addAddress($_PRESANITIZE_REQUEST['email'][$i], $_PRESANITIZE_REQUEST['name-first'][$i] . ' ' . $_PRESANITIZE_REQUEST['name-last'][$i]);     // Add a recipient
    $mail->addReplyTo("info@ligerbots.com", "LigerBots Info");
    
    $mail->isHTML(true);
    $mail->Subject = $_PRESANITIZE_REQUEST['subject'];
    $mail->Body    = $content;
    
    if(!$mail->send()) {
      $action_message .= "Error sending to: " . $_POST['email'][$i] . "\n" . $mail->ErrorInfo . "\n\n";
    } else {
      echo "Email sent to " . $_PRESANITIZE_REQUEST['email'][$i] . "\n";
    }
  }
}
?>
<!DOCTYPE html>
<html>
  <?php page_head( "LigerBots Mailer" ); ?>

  <body>
    <style>
      #message-editable {
        background: white;
        border: 1px solid black;
      }
      #email-status, #email-status tr, #email-status td, #email-status th {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 0.5rem;
      }
    </style>
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
            <div class="row side-margins bottom-margin">
              <div class="col-xs-12">
                
                <?php if($_REQUEST['action'] == "send") { ?>
                <p>Mail sent</p>
                <?php if($action_message != "") {
                    echo "<pre>\n";
                    echo $action_message;
                    echo "\n</pre>";
                  }
                } else if($_REQUEST['action'] == "status") {
                  $q = $wpdb->prepare("SELECT * FROM `email-tracking` WHERE `email-id`=%s", $_REQUEST['id']);
                  $people = $wpdb->get_results($q, ARRAY_A);
                ?>
                <a href="mail.php">Create email</a><br/>
                <?php 
                  $q = $wpdb->prepare("SELECT `subject` FROM `email-tracking-emails` WHERE `id`=%s", $_REQUEST['id']);
                  $email = $wpdb->get_results($q, ARRAY_A);
                ?>
                <h2><?=$email[0]['subject'];?></h2>
                <table id="email-status">
                  <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email Address</th>
                    <th>Time Opened</th>
                  </tr>
                  <?php foreach($people as $row) { ?>
                  <tr>
                    <td><?=$row['name-first'];?></td>
                    <td><?=$row['name-last'];?></td>
                    <td><?=$row['email'];?></td>
                    <td><?=$row['open-time']=="0"?"NOT OPENED":date("m/d/y h:i a", intval($row['open-time']));?></td>
                  </tr>
                  <?php } ?>
                </table>
                <?php
                } else { ?>
                Create email:<br/>
                <form action="mail.php" method="POST">
                  <input type="hidden" name="action" value="send" style="display: none" />
                  <textarea name="content-html" style="display: none"></textarea>
                  <input type="text" name="subject" placeholder="Subject" /><br/>
                  <input type="text" name="from-name" placeholder="From name" /><br/>
                  <input type="text" name="from-email" placeholder="From email" /><br/>
                  <input type="password" name="smtp-password" placeholder="Password" /><br/>
                  Email content (<code>${name}</code> turns into name):<br/>
                  <div contenteditable="true" id="message-editable"></div><br/>
                  <textarea id="email-list" placeholder="Paste table here"></textarea><br/>
                  <table id="emails-table"></table><br/>
                  <button type="button" id="email-clear">Clear table</button><br/>
                  <input type="submit" />
                </form>
                <?php } ?>
                <hr />
                
                Previous emails:<br/>
                <?php
                 $emailList = $wpdb->get_results("SELECT `id`, `subject` FROM `email-tracking-emails` ORDER BY `id` DESC", ARRAY_A);
                 foreach($emailList as $row) {
                   ?>
                   <a href="mail.php?action=status&id=<?=$row['id'];?>"><?=$row['subject'];?></a><br/>
                   <?php
                 }
                ?>
              
              </div>
            </div>
            
            <?php output_footer(); ?>
            
          </div>
        </div>
      </div>
    </div>
    
    <?php page_foot(); ?>
    
    <script>
      var emailsTable = $("#emails-table");
      var emailPaste = $("#email-list");
      $("#email-clear").click(function(){ emailsTable.html(""); emailPaste.show();  });
      emailPaste.change(function(){
        var value = $(this).val();
        $(this).val("");
        var table = value.split(/\r?\n/).map(function(element) { return element.split("\t"); });
        emailsTable.html("<tr><th>First Name</th><th>Last Name</th><th>Email Address</th></tr>");
        for(var row of table) {
          var tr = $("<tr></tr>");
          var fnameInput = $("<input type='text' name='name-first[]' />");
          var lnameInput = $("<input type='text' name='name-last[]' />");
          var emailInput = $("<input type='email' name='email[]' />");
          fnameInput.val(row[0]);
          lnameInput.val(row[1]);
          emailInput.val(row[2]);
          var fnameTd = $("<td></td>");
          var lnameTd = $("<td></td>");
          var emailTd = $("<td></td>");
          fnameTd.append(fnameInput);
          lnameTd.append(lnameInput);
          emailTd.append(emailInput);
          tr.append(fnameTd);
          tr.append(lnameTd);
          tr.append(emailTd);
          emailsTable.append(tr);
        }
        emailPaste.hide();
      });
      
      $("form").submit(function(){
        var contentHtml = $(this).find("#message-editable").html();
        $(this).find("[name=content-html]").val(contentHtml);
      });
    </script>
  </body>
</html>
