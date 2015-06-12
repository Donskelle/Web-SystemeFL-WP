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

require_once( 'Models/Groups.php' );
require_once( 'Models/Documents.php' );

require_once('GUI_Backend/Admin_GUI.php');
require_once('GUI_Backend/Moderator_GUI.php');
require_once('GUI_Backend/User_GUI.php');
require_once('GUI_Backend/Widgets/CustomField.php');

require_once('GUI_Frontend/FrontendController.php');
require_once('GUI_Frontend/Widgets/Menu_Widget.php');


/*
 * register_activation_hook aktiviert sich, wenn das Plugin aktiviert wird.
 * */
register_activation_hook(__FILE__,  'dokumummy_activated');


function dokumummy_activated() {
    /**
     * Rolen registrieren
     */
    new RoleSetup();

    /**
     * Datenbanken für Gruppen erstellen
     */
    $groups = new Groups();
    $groups->initDatabase();

    /**
     * Datenbanken für Dokumente erstellen
     */
    $documents = new Documents();
    $documents->initDatabase();
}

/**
 *  Wenn ein Benutzer nicht eingelogged ist, wird er auf die Loginpage redirected.
 *  Der Test auf is_user_logged_in ist wichtig, da sonst immer auf die Loginpage verviesen wird.
 */
add_action('template_redirect', 'login_redirect');
function login_redirect() {
	if ( ! is_user_logged_in() ) {
		auth_redirect(); //https://codex.wordpress.org/Function_Reference/auth_redirect
    }
}

/**
 * Wird beim Init des Plugins ausgeführt
 */
add_action('init', 'initPlugin');
function initPlugin() {
    buildDashboardGUI();
    controllerInit();
}


function buildDashboardGUI(){
    if(is_user_logged_in()){
        $role = get_user_role(wp_get_current_user());
        buildRoleGUI($role);
    }
}


function controllerInit(){
    if(is_admin()) {
        add_action('load-post.php', 'init_customfield');
        add_action('load-post-new.php', 'init_customfield');
    } else {
        frontendController();
    }
}


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
            break;
    }
}
