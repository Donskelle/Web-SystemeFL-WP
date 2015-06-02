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
require_once('GUI/Widgets/Admin_Widget_Test.php');

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
new Admin_Widget_Test();
add_action('template_redirect', 'login_users');
/**
 *  Wenn ein Benutzer nicht eingelogged ist, wird er auf die Loginpage redirected.
 *  Der Test auf is_user_logged_in ist wichtig, da sonst immer auf die Loginpage verviesen wird.
 */
function login_users() {
	if ( ! is_user_logged_in() ) {
		auth_redirect(); //https://codex.wordpress.org/Function_Reference/auth_redirect
	}else{

		//Der Hook nach dem login ist wp_login https://codex.wordpress.org/Plugin_API/Action_Reference/wp_login
		//DO GUI STUFF CONTROLLER
	}
}

