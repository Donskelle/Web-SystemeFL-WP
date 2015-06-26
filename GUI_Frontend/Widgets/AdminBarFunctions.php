<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 15:48
 */

class AdminBarFunctions {




    public function __construct(){
        add_action('admin_bar_menu', array('this','addFunctionality'));
    }


    private function addFunctionality($wp_admin_bar){
        $args = array(
            'id'    => 'my_page',
            'title' => 'My Page',
            'href'  => 'http://mysite.com/my-page/',
            'meta'  => array( 'class' => 'my-toolbar-page' )
        );
        $wp_admin_bar -> add_node($args);
    }



}