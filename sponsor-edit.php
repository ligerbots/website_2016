<?php
require_once( 'include/page_elements.php' );
require_once( 'include/sponsor_utils.php' );

/* Short and sweet */
define('WP_USE_THEMES', false);
require_once( 'wp-backend/wp-blog-header.php' );
http_response_code(200); // override wp

// must be logged in
if ( ! is_user_logged_in() )
{
    header('Location: /login.php?r=%2fsponsor-edit.php');
    die();
}

// Higher security level?
if ( ! current_user_can( 'edit_posts' ) ) {
    header('Location: /sponsors.php');
    die();
}

$icon_set_name = 'production';
$icon_sets = fetch_sponsor_icon_sets();

$message = '';
if ( isset( $_GET[ 'icon_set' ] ) )
{
    $new_name = $_GET[ 'icon_set' ];
    if (! in_array($new_name, $icon_sets))
        $message = "Unknown icon set '$new_name'";
    else
        $icon_set_name = $new_name;
}
if ( isset( $_GET[ 'edit_set' ] ) )
{
    $edit_set = strtolower($_GET[ 'edit_set' ]);
} else {
    $edit_set = 'sponsor_page';
}

if ( isset( $_POST[ 'load_icon_set' ] ) )
{
    $new_icon = $_POST[ 'select_icon_set' ];
    if (! in_array($new_icon, $icon_sets))
    {
        $message = "Unknown icon set '$new_icon'";
    }

    $new_edit = $_POST[ 'select_edit_set' ];
    if (! in_array($new_edit, $EDIT_SETS))
    {
        $message = "Unknown edit set '$new_edit'";
    }

    if (strlen($message) == 0)
    {
        header('Location: ' . $_SERVER['PHP_SELF'] . "?icon_set=$new_icon&edit_set=$new_edit");
        die();
    }        
}

if ( isset( $_POST[ 'copy_icon_set' ] ) )
{
    $new_name = $_POST[ 'new_set_name' ];
    if (in_array($new_name, $icon_sets))
        $message = "Icon set '$new_name' exists";
    else
    {
        $message = copy_icon_set($icon_set_name, $new_name);
        if (strlen($message) == 0)
        {
            // success. switch to it
            header('Location: ' . $_SERVER['PHP_SELF'] . "?icon_set=$new_name&edit_set=$edit_set");
            die();
        }
    }
}

if (strlen($icon_set_name) == 0) $icon_set_name = 'production';
$is_editable = $icon_set_name != 'production';

if ( $is_editable && isset( $_POST[ 'add_row' ] ) )
{
    add_sponsor_row($icon_set_name);
    // reload after adding a row
    header('Location: ' . $_SERVER['PHP_SELF'] . "?icon_set=$icon_set_name&edit_set=$edit_set#retired");
    die();
}

if ( isset( $_POST[ 'action' ] ) && isset( $_POST[ 'id' ] ) )
{
    $input = filter_input_array(INPUT_POST);
    edit_sponsor_row($input);
    die();
}

if ( $is_editable && isset( $_POST[ 'set_as_production' ] ) )
{
    $message = set_iconset_as_production($icon_set_name);
    if (strlen($message) == 0)
    {
        // success. Switch to production
        header('Location: ' . $_SERVER['PHP_SELF']);
        die();
    }
}

if ($edit_set == 'sponsor_page')
    $columns = $EDIT_SPONSOR_PAGE_COLUMNS;
else if ($edit_set == 'sponsor_bar')
    $columns = $EDIT_SPONSOR_BAR_COLUMNS;
else
    $columns = $EDIT_SPONSOR_COLUMNS;
$ncolumns = count($columns);

$sponsor_set = fetch_sponsor_info($icon_set_name);

?>

<!DOCTYPE html>
<html>
  <?php
  page_head( "Edit Sponsor Info", false, NULL, $css ); 
  ?>
  <style>
   .btn-default {padding-left: 3px; padding-right: 3px;}
   .form-control {padding-left: 3px; padding-right: 3px;}
  </style>
  <body>
    <div id="header-ghost" ></div>
    <div class="container-fluid no-side-padding">
      <div class="col-xs-12 no-side-padding">

        <?php 
        output_header(); 
        output_navbar();
        ?>
        
        <div class="row page-body">
          <div class="col-xs-12">
            <div class="row top-spacer"> </div>
            <div class="row bottom-margin">
              <center><div class="notindex-title">EDIT SPONSOR INFORMATION</div></center>
            </div>
            
            <?php
            if ( ! empty( $message ) )
            {
                echo '<div class="row">';
                echo '<div class="alert alert-danger"><strong>' . $message . '</strong></div>';
                echo "</div>\n";
            }
            ?>

            <div class="row">
              <div class="col-xs-12">
                <?php 
                echo '<form class="form-inline" action="' . $_SERVER['PHP_SELF'] . 
                     "?icon_set=$icon_set_name&edit_set=$edit_set\" method=\"post\">\n";
                ?>
                <label for="select_icon_set">&nbsp;Icon set:</label>
                <select class="form-control" id="select_icon_set" name="select_icon_set">
                  <?php
                  foreach ($icon_sets as $s)
                  {
                      $sel = '';
                      if ($s == $icon_set_name) $sel = ' selected';
                      echo "  <option $sel>$s</option>\n";
                  }
                  ?>
                </select>
                <select class="form-control" id="select_edit_set" name="select_edit_set">
                  <?php
                  foreach ($EDIT_SETS as $s)
                  {
                      $sel = '';
                      if ($s == $edit_set) $sel = ' selected';
                      echo "  <option $sel>$s</option>\n";
                  }
                  ?>
                </select>
                <button type="submit" name="load_icon_set" class="btn btn-default">Load</button>
                &nbsp;&nbsp;
                <button type="submit" name="copy_icon_set" class="btn btn-default">Copy Icon Set</button>
                <input class="form-control" id="new_set_name" name="new_set_name" placeholder="new_name">
                <?php
                if ($is_editable)
                {
                    echo '&nbsp;&nbsp;<button type="submit" name="add_row" class="btn btn-default">Add Row</button>' . "\n";
                }
                if ($is_editable && wp_get_current_user()->user_login == 'prensing')
                {
                    echo '&nbsp;&nbsp;<button type="submit" name="set_as_production" class="btn btn-default">Set As Prod</button>' . "\n";
                }
                echo '&nbsp;&nbsp;<a href="/sponsors.php?icon_set=' . $icon_set_name . '" target="_blank">View Sponsor Page</a>' . "\n";
                ?>
                  </form>
              </div>
            </div>

            <div class="row">
              <div class="table">
                <table id="sponsor-table" class="table table-bordered table-condensed">
                  <?php
                  echo "<thead>\n";
                  $in_head = True;
                  echo "<tr>\n";
                  foreach ($columns as $c)
                  {
                      $t = str_replace('_', ' ', $c);
                      $cls = '';
                      if ($c == 'id') $cls = 'style="display:none"';
                      echo "<th $cls>$t</th>\n";
                  }
                  echo "</tr>\n";
                  echo "<br/>\n";

                  if ($edit_set == 'sponsor_bar')
                  {
                      if ($in_head) { echo "</thead><tbody>\n"; $in_head = False; }
                      $rows = sponsor_bar_rows($sponsor_set, false);

                      foreach ($rows as $spr)
                      {
                          foreach ($columns as $c)
                          {
                              $cls = '';
                              if ($c == 'id') $cls = 'style="display:none"';
                              echo "<td $cls>" . $spr[$c] . "</td>\n";
                          }
                          echo "</tr>\n";                          
                      }
                  }
                  else
                  {
                      uksort($sponsor_set, 'sort_by_level_name');
                      foreach ($sponsor_set as $skey => $row_set)
                      {
                          if (! $in_head) { echo "</tbody><thead>\n"; $in_head = True; }
                          echo "<tr id=\"$skey\"><th style=\"display:none\"/><th colspan=\"$ncolumns-1\">" . ucwords($skey) . "</th></tr>\n";
                          
                          ksort($row_set);
                          $curr_row = 0;
                          foreach ($row_set as $rk => $row)
                          {
                              uasort($row, 'sort_by_column');
                              foreach ($row as $spr)
                              {
                                  if ($spr['layout_row'] != $curr_row)
                                  {
                                      if ($curr_row > 0)
                                      {
                                          // emphasize the layout row change
                                          if (! $in_head) { echo "</tbody><thead>\n"; $in_head = True; }
                                          echo "<tr style='font-size: 1px;'><td style=\"display:none\"/><td colspan='$ncolumns-1'>&nbsp;</td></tr>\n";
                                      }
                                      $curr_row = $spr['layout_row'];
                                  }
                                  
                                  if ($in_head) { echo "</thead><tbody>\n"; $in_head = False; }
                                  echo "<tr>\n";
                                  foreach ($columns as $c)
                                  {
                                      $cls = '';
                                      if ($c == 'id') $cls = 'style="display:none"';
                                      echo "<td $cls>" . $spr[$c] . "</td>\n";
                                  }
                                  echo "</tr>\n";
                              }
                          }
                      }
                  }
                  echo "</tbody>\n";
                  ?>
                </table>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-10 col-xs-offset-1">
                <center><b>Help</b></center>
                <b>Sponsor Info Settings</b>
                <ul>
                  <li>sponsor_level - puma, cheetah, etc. Use "retired" to hide an entry.</li>
                  <li>name - name of sponsor. Used as page text if no logo provided (eg Lynx sponsors).</li>
                  <li>url - destination link for sponsor</li>
                  <li>icon - graphic icon file. Must be under /images/sponsor-logos in web tree.</li>
                </ul>
                <b>Sponsor Page Settings</b>
                <ul>
                  <li>layout_row - Row number within sponsor level</li>
                  <li>layout_column - Column number with the row</li>
                  <li>layout_order - Order in the layout. Only affects the "mobile" layout order</li>
                  <li>md_width - Percentage width of icon width on standard page</li>
                  <li>md_extra_push - <em>Extra</em> percentage width to the left of the icon on the standard page</li>
                  <li>xs_columns - Number of columns (integer; out of <em>12</em>) for the entry on the mobile page</li>
                  <li>xs_offset - Number of columns (integer; out of 12) to the left of the entry on the mobile page. 
                    Should be (12 - xs_columns)/2 (integer!). </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php page_foot(); ?>
    <script type="text/javascript" src="/js/jquery.tabledit.js" ></script>
    <?php
    if ($is_editable)
    {
        echo '<script type="text/javascript">' . "\n";
        echo "$('#sponsor-table').Tabledit({\n";
        echo " url: '/sponsor-edit.php',\n";
        //echo " inputClass: 'form-control',\n";
        echo " editButton: false,\n";
        echo " deleteButton: false,\n";
        echo " saveButton: false,\n";
        echo " hideIdentifier: true,\n";
        echo " columns: {\n";
        echo "     identifier: [0, 'id'],\n";
        echo '     editable: [';
        $i = 0;
        foreach ($columns as $c)
        {
            if ($c == 'sponsor_level')
            {
                echo "[$i, '$c', '{";
                $ii = 0;
                foreach ($level_order as $ln => $li)
                {
                    if ($ii++ > 0) echo ',';
                    echo "\"$ln\":\"$ln\"";
                }
                echo "}'],\n";
            }
            else if ($c != 'id') echo "[$i, '$c'],";
            $i++;
        }
        echo "]}});\n";
        echo "</script>\n";
    }
    ?>

  </body>
</html>
