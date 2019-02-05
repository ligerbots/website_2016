<?php

// Routines to query and format sponsor pages

function fetch_sponsor_info($setname)
{
    global $wpdb;

    $sql = "SELECT * FROM sponsor_info WHERE icon_set='$setname' order by sponsor_level, layout_row";
    
    // $q = $wpdb->prepare($sql);
    
    // Execute the query, return results as an array of dictionaries
    $dbrows = $wpdb->get_results($sql, ARRAY_A);

    $set = [];
    foreach ($dbrows as $row)
    {
        $l = $row['sponsor_level'];
        if (! array_key_exists($l, $set)) $set[$l] = [];

        $r = $row['layout_row'];
        if (! array_key_exists($r, $set[$l])) $set[$l][$r] = [];
        
        array_push($set[$l][$r], $row);
    }

    return $set;
}

function fetch_sponsor_icon_sets()
{
    global $wpdb;

    $sql = "SELECT DISTINCT icon_set FROM sponsor_info ORDER BY icon_set";
    #$q = $wpdb->prepare($sql);
    
    // Execute the query, return results as an array of dictionaries
    $res = [];
    foreach ($wpdb->get_results($sql, ARRAY_N) as $row)
    {
        array_push($res, $row[0]);
    }
    return $res;
}

function edit_columns()
{
    return ['id', 'sponsor_level', 'name', 'layout_row', 'layout_column', 'layout_order',
            'md_width', 'md_extra_push', 'xs_columns', 'xs_offset', 'url', 'icon'];
}

$EDIT_SETS = ['sponsor', 'sponsor_page'];
$EDIT_SPONSOR_COLUMNS = ['id', 'sponsor_level', 'name', 'url', 'icon'];
$EDIT_SPONSOR_PAGE_COLUMNS = ['id', 'sponsor_level', 'name', 'layout_row', 'layout_column', 'layout_order',
                              'md_width', 'md_extra_push', 'xs_columns', 'xs_offset'];

function edit_sponsor_row($input)
{
    global $wpdb;

    // error_log("edit_sponsor_row: " . print_r($input, TRUE));

    $sqlinput = $input;   // copy

    // remove id and action
    $action = $input['action'];
    unset($sqlinput['action']);
    
    $where = [];
    $where['id'] = $input['id'];
    unset($sqlinput['id']);
    
    if ($action == 'edit') {
        // error_log("row edit id:" . print_r($where,TRUE) . " data=" . print_r($sqlinput,TRUE));
        $wpdb->update('sponsor_info', $sqlinput, $where);
    } else if ($action == 'delete') {
        //$wpdb->delete('sponsor_info', array( 'ID' => $id ));
    }
}

function add_sponsor_row($icon_set)
{
    global $wpdb;

    $input = [];
    $input['icon_set'] = $icon_set;
    $input['sponsor_level'] = 'retired';
    $input['name'] = 'new sponsor';
    $wpdb->insert('sponsor_info', $input);
}

// Comparison functions
function sort_by_column($a, $b)
{
    $ac = $a['layout_column'];
    $bc = $b['layout_column'];
    if ($ac == $bc) return 0;
    return ($ac < $bc) ? -1 : 1;
}
function sort_by_layout($a, $b)
{
    $ac = $a['layout_order'];
    $bc = $b['layout_order'];
    if ($ac == $bc) return 0;
    return ($ac < $bc) ? -1 : 1;
}

// This sorts sponsor levels, not single rows
$level_order = ['puma' => 1, 'panther' => 2, 'cheetah' => 3, 'lynx' => 4, 'new' => 5, 'retired' => 6];
function sort_by_level_name($a, $b)
{
    global $level_order;
    
    $ai = $level_order[$a];
    $bi = $level_order[$b];
    if ($ai == $bi) return 0;
    return ($ai < $bi) ? -1 : 1;
}

function set_pushpulls($all_rows)
{
    foreach ($all_rows as $skey => $row_set)
    {
        foreach ($row_set as $rkey => $row)
        {
            # Sort the row by column and figure out what "push" is needed
            uasort($row, 'sort_by_column');
            $push = 0;
            $extrapush = 0;
            foreach ($row as $key => $spr)
            {
                $c = $spr['layout_column'];
                $l = $spr['layout_order'];

                $extrapush += $spr['md_extra_push'];
                if ($c < $l)
                    $push += $spr['md_width'];
                else if ($c > $l || $extrapush > 0)
                {
                    // can't modify the iteration variable; use the keys
                    $all_rows[$skey][$rkey][$key]['push'] = $push + $extrapush;
                }
            }
        }
    }

    # Sort the row by layout order and figure out pulls
    foreach ($all_rows as $skey => $row_set)
    {
        foreach ($row_set as $rkey => $row)
        {
            uasort($row, 'sort_by_layout');
            $pull = 0;
            foreach ($row as $key => $spr)
            {
                if ($spr['push'] != 0)
                    $pull += $spr['md_width'];
                if ($spr['layout_column'] < $spr['layout_order'])
                    $all_rows[$skey][$rkey][$key]['pull'] = $pull;
            }
        }
    }

    // this was modified. Return it.
    return $all_rows;
}

function sponsor_page_css($all_rows)
{
    $done = [];
    $res = "<style>\n@media (min-width: 768px) {\n";
    foreach ($all_rows as $row_set)
    {
        foreach ($row_set as $row)
        {
            foreach ($row as $spr)
            {
                $w = $spr['md_width'];
                $s = "spr-col-p$w";
                if (! array_key_exists($s, $done))
                {
                    $res .= " .$s { width: $w%; margin-left: 0; }\n";
                    $done[$s] = 1;
                }

                $p = $spr['push'];
                if ($p != 0)
                {
                    $s = "spr-push$p";
                    if (! array_key_exists($s, $done))
                    {
                        $res .= " .$s { left: $p%; }\n";
                        $done[$s] = 1;
                    }
                }
                
                $p = $spr['pull'];
                if ($p != 0)
                {
                    $s = "spr-pull$p";
                    if (! array_key_exists($s, $done))
                    {
                        $res .= " .$s { right: $p%; }\n";
                        $done[$s] = 1;
                    }
                }
            }
        }
    }
    $res .= "}\n</style>\n";

    return $res;
}

function sponsor_logo_rows($row_set)
{
    ksort($row_set);
    foreach ($row_set as $rk => $row)
    {
        $last = count($row) - 1;
        
        # Sort the row by layout order and output
        uasort($row, 'sort_by_layout');
        echo '<div class="row spr-wide-row">' . "\n";
        foreach ($row as $spr)
        {
            $cls = 'spr-logo';
            if ($spr['layout_column'] == 0) $cls .= ' spr-lmargin-0';
            if ($spr['layout_column'] == $last) $cls .= ' spr-rmargin-0';

            if ($spr['push'] != 0) $pushpull = ' spr-push' . $spr['push'];
            else if ($spr['pull'] != 0) $pushpull = ' spr-pull' . $spr['pull'];
            else $pushpull = '';

            $style = '';
            if ($spr['top_margin'] != 0) $style=' style="margin-top: ' . $spr['top_margin'] . 'px;"';
            
            echo '  <div class="spr-col-p' . $spr['md_width'] . $pushpull . ' col-xs-' . $spr['xs_columns'] . ' col-xs-offset-' . $spr['xs_offset'] . ' vcenter">' . "\n";
            if ($spr['url'] != '') echo '    <a href="' . $spr['url'] . '" target="_blank">';
            echo '<img class="' . $cls . '"' . $style . ' src="/images/sponsor-logos/' . $spr['icon'] . '" alt="' . $spr['name'] . '" title="' . $spr['name'] .'" />';
            if ($spr['url'] != '') echo '</a>';
            echo "\n  </div>\n";
        }
        echo "</div>\n";
    }
}

function sponsor_text_rows($row_set)
{
    ksort($row_set);
    foreach ($row_set as $rk => $row)
    {
        $last = count($row) - 1;
        
        # Sort the row by layout order and output
        uasort($row, 'sort_by_layout');
        echo '<div class="row spr-wide-row">' . "\n";
        foreach ($row as $spr)
        {
            if ($spr['push'] != 0) $pushpull = ' spr-push' . $spr['push'];
            else if ($spr['pull'] != 0) $pushpull = ' spr-pull' . $spr['pull'];
            else $pushpull = '';

            echo '  <div class="donor-name spr-col-p' . $spr['md_width'] . $pushpull . ' col-xs-' . $spr['xs_columns'] . ' col-xs-offset-' . $spr['xs_offset'] . '">';
            if ($spr['url'] != '') echo '    <a href="' . $spr['url'] . '" target="_blank">';
            echo $spr['name'];
            if ($spr['url'] != '') echo '</a>';
            echo "\n  </div>\n";
        }
        echo "</div>\n";
    }
}

function copy_icon_set($current_set, $new_name)
{
    global $wpdb;

    if (strlen($new_name) == 0) return '';
    $cols = ['icon_set', 'sponsor_level', 'name', 'layout_row', 'layout_column', 'layout_order',
             'md_width', 'md_extra_push', 'xs_columns', 'xs_offset', 'url', 'icon'];
    
    $sql = 'INSERT INTO sponsor_info (' . implode(',', $cols) . ") SELECT '$new_name',". implode(',', array_slice($cols, 1));
    $sql .= " FROM sponsor_info WHERE icon_set='$current_set';";
    $wpdb->query($sql);
    return '';
}

function set_iconset_as_production($current_set)
{
    global $wpdb;

    $oldname = 'production_' . strftime('%Y%m%d_%H%M%S');
    $wpdb->query('START TRANSACTION;');
    $res1 = $wpdb->query("UPDATE sponsor_info SET icon_set='$oldname' WHERE icon_set='production';");
    $res2 = $wpdb->query("UPDATE sponsor_info SET icon_set='production' WHERE icon_set='$current_set';");
    if ($res1 && $res2)
    {
        $wpdb->query('COMMIT;');
    }
    else
    {
        $wpdp->query('ROLLBACK;');
    }

    //return $sql;
    //$wpdb->query($sql);
    return '';
}
?>
