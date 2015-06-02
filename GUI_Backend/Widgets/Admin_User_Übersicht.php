<?php
/**
 * Created for DokuMummy_Plugin.
 * User: Jan
 * Date: 02.06.2015
 * Time: 12:38
 */
require_once('CustomWidget.php');
class Admin_User_Übersicht extends CustomWidget{


    public function __construct(){
        parent::__construct('admin_user_übersicht', 'Admin User Übersicht');
    }

    /**
     * Die Callbackfunktion des Widgets. Nur diese Funktion muss implementiert werden.
     * @return mixed
     */
    public function widget_content()
    {
        echo "Übersicht der User (Adminbereich)";
        // TODO: Implement widget_content() method.
    }

}