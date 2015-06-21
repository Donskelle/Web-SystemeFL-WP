<?php
/**
 * Created by PhpStorm.
 * User: KRa
 * Date: 01.06.2015
 * Time: 21:49
 */

require_once('Widgets/ModGruppenÜbersicht.php');

/**
 * Class ModeratorGUI
 * //TODO: Moderator Funktionen in Dashboard/Widgets einbauen.
 */
class ModeratorGUI {

    public function __construct(){
        new ModGruppenÜbersicht();
    }




}