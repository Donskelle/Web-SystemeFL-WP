<?php
/**
 * Created for DokuMummy_Plugin.
 * User: Jan
 * Date: 02.06.2015
 * Time: 11:46
 */
require_once('CustomWidget.php');
class ModGruppenÜbersicht extends CustomWidget {

    public function __construct(){
        parent::__construct('modGruppenÜbersicht', 'Moderator-Gruppen-Übersicht');
    }
    /**
     * Die Callbackfunktion des Widgets. Nur diese Funktion muss implementiert werden.
     * @return mixed
     */
    public function widget_content()
    {
        echo 'Dies ist eine Übersicht der Gruppen, die dieser Moderator betreut.';

        echo '<ul>';
        for($i = 0; $i<10; $i++){
            echo '<li>'.$i.'</li>';
        }
        echo '</ul>';
        // TODO: Datenbank abfragen und darstellen.
    }
}