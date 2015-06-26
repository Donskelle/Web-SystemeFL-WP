<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 14:05
 */

class Newsfeed_Widget extends CustomWidget{


    public function __construct(){
        parent::__construct('newsfeed_widget', 'Newsfeed');
    }

    /**
     * Die Callbackfunktion des Widgets. Nur diese Funktion muss implementiert werden.
     * @return mixed
     */
    public function widget_content()
    {
        echo '<script>console.log("hallo")</script>';
    }

}