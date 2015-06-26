<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 15:48
 */

class AdminBarDeleteButton {



//nur im Dokument view starten.
    public function __construct(){
        add_action('admin_bar_menu', array($this,'addDelete'),998); ///999 ist die Priorität
    }


    public function addDelete($wp_admin_bar){

        $delete = array(
            'id'    => 'delete_document',
            'title' => 'Dokument Löschen',
            'meta'  => array(
//                'html' => '',
//                'class' => 'my-toolbar-page'
            )
        );
        $wp_admin_bar -> add_node($delete);
    }
}