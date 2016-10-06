<?php
/**
 * @package LigerBots
 * @version 1.0
 */
/*
 * Plugin Name: LigerBots
 * Description: Testing a plugin
 * Author: Paul Rensing
 * Version: 1.0
 */

//include $_SERVER['DOCUMENT_ROOT'] . '/include/google.php';

/* Hook the User approval to add the user to the Google groups. */
add_action( 'wpau_approve', 'add_user_to_google_groups', 10, 1 );

/*
 * Sends email to Administrator after user registration is approved
 * 
 * @param int $user_id User id of user whose registration is just approved
 */
function add_user_to_google_groups( $user_id )
{
    $user = new WP_User($user_id);

    $user_email = $user->user_email;

    $school = $user->get( 'school' );
    if ( strtoupper($school) == 'NONE' ) $school = '';
    $roles = $user->get( 'team_role' );

    $groups = array();
    if ( in_array( 'Student', $roles ) )
    {
        array_push( $groups, "student_$school@ligerbots.com" );
    }
    else
    {
        $isParent = in_array( 'Parent', $roles );
        if ( $isParent )
            array_push( $groups, "parent_$school@ligerbots.com" );
        if ( in_array( 'Mentor', $roles ) )
        {
            if ( $isParent )
                array_push( $groups, "mentor_parent@ligerbots.com" );
            else
                array_push( $groups, "mentor_other@ligerbots.com" );
        }
        if ( in_array( 'Coach', $roles ) )
        {
            array_push( $groups, "coaches@ligerbots.com" );
        }
    }
    
    $message  = "New user registration on Ligerbots\n";
    $message .= "E-mail: $user_email\n";
    $message .= "Groups: " . join( ', ', $groups ) . "\n";

    @wp_mail( get_option('admin_email'), "[LigerBots] New User Approval", $message );
}

?>
