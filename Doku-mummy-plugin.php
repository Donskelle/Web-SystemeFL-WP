<?php
/*
Plugin Name: Doku Mummy Plugin
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: KRa
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/


require_once('Administration/RoleManager.php');

/*
 * Anscheinend können neue Rollen nur mit Lösungen, die ich nicht kenne, aus Dateien, die nicht die "PluginDatei" sind
 * gesetzt werden. Daher muss dieser Teil in dieser Datei bleiben.
 *
 * register_activation_hook aktiviert sich, wenn das Plugin installiert wird.
 *
 * */
register_activation_hook(__FILE__,  'register_roles');


function register_roles() {
	new RoleManager();
	//Entfernt die Customeroles.
	/*remove_role('dokuUser');
	remove_role('dokuModerator');
	remove_role('dokuAdmin');*/
}
https://codex.wordpress.org/Function_Reference/auth_redirect

add_action('template_redirect', 'redirect_not_logged_in_users');
/**
 *  Wenn ein Benutzer nicht eingelogged ist, wird er auf die Loginpage redirected.
 *  Der Test auf is_user_logged_in ist wichtig, da sonst immer auf die Loginpage verviesen wird.
 */
function redirect_not_logged_in_users() {
	if ( ! is_user_logged_in() ) {
		auth_redirect();
	}
}