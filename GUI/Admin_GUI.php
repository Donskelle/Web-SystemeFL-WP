<?php
/**
 * Created by PhpStorm.
 * User: KRa
 * Date: 01.06.2015
 * Time: 21:49
 */
require_once('Widgets/Admin_User_Übersicht.php');
require_once('Widgets/Admin_Widget_Test.php');


class Admin_GUI {
//TODO: Admin Funktionen in Dashboard/Widgets einbauen.
    public function __construct(){
        new Admin_Widget_Test();
        new Admin_User_Übersicht();
    }
}