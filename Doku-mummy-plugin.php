<?php
/*
Plugin Name: Doku Mummy Plugin
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Jan und Fabian (Das doppelte Duo!)
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/


require_once( 'Administration/RoleSetup.php' );
require_once('GUI_Backend/Admin_GUI.php');
require_once('GUI_Backend/Moderator_GUI.php');
require_once('GUI_Backend/User_GUI.php');
/*
 * Anscheinend können neue Rollen nur mit Lösungen, die ich nicht kenne, aus Dateien, die nicht die "PluginDatei" sind
 * gesetzt werden. Daher muss dieser Teil in dieser Datei bleiben.
 *
 * register_activation_hook aktiviert sich, wenn das Plugin installiert wird.
 *
 * */
register_activation_hook(__FILE__,  'register_roles');


function register_roles() {
	new RoleSetup();
	//Entfernt die Customeroles.
	/*remove_role('dokuUser');
	remove_role('dokuModerator');
	remove_role('dokuAdmin');*/
}



add_action('template_redirect', 'login_users');
/**
 *  Wenn ein Benutzer nicht eingelogged ist, wird er auf die Loginpage redirected.
 *  Der Test auf is_user_logged_in ist wichtig, da sonst immer auf die Loginpage verviesen wird.
 */
function login_users() {
	if ( ! is_user_logged_in() ) {
		auth_redirect(); //https://codex.wordpress.org/Function_Reference/auth_redirect
    }
}

add_action('init', 'buildDashboardGUI');
/**
 * Gibt die Rolle des Users als String zurück.
 * @param WP_USER $user
 * @return string
 */
function get_user_role($user){
    return $user->roles[0];
}



/**
 *
 *
 * @param string $role
 */
function buildRoleGUI($role){
    switch ($role){
        case 'dokuAdmin':
            new Admin_GUI();
            break;
        case 'dokuModerator':
            new ModeratorGUI();
            break;
        case 'dokuUser':
            new User_GUI();
            break;
        default:
           // new Admin_GUI();
            break;
    }
}
function buildDashboardGUI(){
    if(is_user_logged_in()){
        $role = get_user_role(wp_get_current_user());
        buildRoleGUI($role);
    }
}

function frontendController(){

}
