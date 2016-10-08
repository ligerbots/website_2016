<?php
// deal with wordpress escaping quotes automatically by saving the request array
// before wordpress gets a chance to mess with it
// this assignment copies the array
$_PRESANITIZE_REQUEST = $_REQUEST;

require_once( 'include/page_elements.php' );
require_once 'include/RandomStringGenerator.php';
$rnd = new RandomStringGenerator();
if(!class_exists('PHPMailer')) {
  require_once 'wp-backend/wp-content/plugins/gmail-smtp/PHPMailer/class.phpmailer.php';
  require_once 'wp-backend/wp-content/plugins/gmail-smtp/PHPMailer/class.smtp.php';
}

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
  $toGroups = json_decode($_PRESANITIZE_REQUEST['to-groups'], true);
  
  $q = $wpdb->prepare("INSERT INTO `email-tracking-emails` (`subject`, `content`) VALUES(%s, %s)", $_PRESANITIZE_REQUEST['subject'], $_PRESANITIZE_REQUEST['content-html']);
  $wpdb->query($q);
  $id = $wpdb->get_results("SELECT LAST_INSERT_ID() FROM `email-tracking-emails`", ARRAY_A)[0]['LAST_INSERT_ID()'];
  
  foreach($toGroups as $email=>$member) {
    $nameFirst = "";
    if(array_key_exists("name-first", $member)) {
      $nameFirst = $member["name-first"];
    }
    $nameLast = "";
    if(array_key_exists("name-last", $member)) {
      $nameLast = $member["name-last"];
    }
    $nameOverride = "";
    if(array_key_exists("name-override", $member)) {
      $nameOverride = $member["name-override"];
    } else {
      $nameOverride = $nameFirst;
    }
    
    $tracking_id = $rnd->generate(32);
    
    $imgTag = "<img src='$protocol://$hostname/mail/$tracking_id.gif' />";
    $content = $_PRESANITIZE_REQUEST['content-html'];
    $content = str_replace('${name}', htmlspecialchars($nameOverride), $content);
    $content .= $imgTag;
    
    $mail = new PHPMailer;

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
    $mail->addAddress($email, $nameFirst . ' ' . $nameLast);
    $mail->addReplyTo("info@ligerbots.com", "LigerBots Info");
    
    $mail->isHTML(true);
    $mail->Subject = $_PRESANITIZE_REQUEST['subject'];
    $mail->Body    = $content;
    
    if(!$mail->send()) {
      $action_message .= "Error sending to: " . $email . "\n" . $mail->ErrorInfo . "\n\n";
    } else {
      $q = $wpdb->prepare(
        "INSERT INTO `email-tracking` (`id`, `email-id`, `name-first`, `name-last`, `email`) VALUES(%s, %s, %s, %s, %s)",
        $tracking_id,
        $id,
        $nameFirst,
        $nameLast,
        $email);
      $wpdb->query($q);
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
      #email-status, #email-status tr, #email-status td, #email-status th,
      #group-preview-table, #group-preview-table tr, #group-preview-table td, #group-preview-table th {
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
                ?>
                <br/><a href="mail.php">Create email</a>
                <?php } else if($_REQUEST['action'] == "status") {
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
                  <input type="hidden" name="to-groups" style="display: none" />
                  <textarea name="content-html" style="display: none"></textarea>
                  <input type="text" name="subject" placeholder="Subject" /><br/>
                  <input type="text" name="from-name" placeholder="From name" /><br/>
                  <input type="text" name="from-email" placeholder="From email" /><br/>
                  <input type="password" name="smtp-password" placeholder="Password" /><br/>
                  Email content (<code>${name}</code> turns into name):<br/>
                  <div contenteditable="true" id="message-editable"></div><br/>
                  
                  <div id="to-group">
                    <ul>
                      <li id="team-groups">
                        <label><input type="checkbox" class="team-select" /> team@ligerbots.com</label>
                        <ul>
                          <li><label><input type="checkbox" class="group-select" data-group="parents_south" /> parents_south@ligerbots.com</label></li>
                        </ul>
                        <ul>
                          <li><label><input type="checkbox" class="group-select" data-group="parents_north" /> parents_north@ligerbots.com</label></li>
                        </ul>
                        <ul>
                          <li><label><input type="checkbox" class="group-select" data-group="coaches" /> coaches@ligerbots.com</label></li>
                        </ul>
                        <ul>
                          <li><label><input type="checkbox" class="group-select" data-group="mentors_other" /> mentors_other@ligerbots.com</label></li>
                        </ul>
                        <ul>
                          <li><label><input type="checkbox" class="group-select" data-group="students_south" /> students_south@ligerbots.com</label></li>
                        </ul>
                        <ul>
                          <li><label><input type="checkbox" class="group-select" data-group="students_north" /> students_north@ligerbots.com</label></li>
                        </ul>
                      </li>
                      <li>
                        <label><input type="checkbox" class="group-select" data-group="incoming" /> Incoming Members</label>
                      </li>
                    </ul>
                  </div>
                  <button id="email-submit" type="submit">Send</button><br/><br/>
                  <div id="group-preview">
                    <table id="group-preview-table">
                      <tr><th>Value of ${name}</th><th>First name</th><th>Last name</th><th>Email</th></tr>
                    </table>
                  </div>
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
      var loadReq;
      var submit = $("#email-submit");
      
      function loadGroups() {
        submit.prop('disabled', true);
        if(loadReq) loadReq.abort();
        var querystring = [];
        $(".group-select").each(function(){
          if($(this).prop("checked")) querystring.push($(this).attr("data-group"));
        });
        
        loadReq = $.get("/mail/groups-get.php?" + querystring.join("&"), function(data) {
          $("#group-preview").html('<table id="group-preview-table"><tr><th>Value of ${name}</th><th>First name</th><th>Last name</th><th>Email</th></tr></table>');
          for(var email in data) {
            var member = data[email];
            var overrideName = member['name-override'] || member['name-first'] || "";
            $("#group-preview-table").append("<tr><td>" + overrideName + "</td><td>" + (member['name-first'] || "") + "</td><td>" + (member['name-last'] || "") + "</td><td>" + member['email'] + "</td></tr>");
          }
          $("[name=to-groups]").val(JSON.stringify(data));
          submit.prop('disabled', false);
        }).fail(function(){
          $("#group-preview").html("Error loading members");
        });
      }
    
      $(".team-select").change(function(){
        $("#team-groups ul input[type=checkbox]").prop("checked", $(this).prop("checked"));
        loadGroups();
      });
      $("#team-groups ul input[type=checkbox]").change(function(){
        var isChecked = $("#team-groups ul input[type=checkbox]:checked").length > 0;
        var isUnchecked = $("#team-groups ul input[type=checkbox]:not(:checked)").length > 0;
        if(isChecked && isUnchecked) {
           $(".team-select").prop("checked", false).prop("indeterminate", true);
        } else if(isChecked) {
          $(".team-select").prop("checked", true).prop("indeterminate", false);
        } else {
          $(".team-select").prop("checked", false).prop("indeterminate", false);
        }
        loadGroups();
      });
      $(".group-select[data-group=incoming]").change(function(){
        loadGroups();
      });
      
      $("form").submit(function(){
        var contentHtml = $(this).find("#message-editable").html();
        $(this).find("[name=content-html]").val(contentHtml);
      });
    </script>
  </body>
</html>
