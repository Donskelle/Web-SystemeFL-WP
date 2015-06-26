<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 18:02
 */

class AdminBarLayoutSelection {
    private $current_layout;
    public function __construct($currentLayout){
        //$this->current_layout = $currentLayout;
        add_action('admin_bar_menu', array($this,'showLayoutSelection'), 999); ///998 ist die Priorität
    }

    public function showLayoutSelection($wp_admin_bar){

        $layout_parent = array(
                'id' => 'select_layout',
                'title' => 'Layout Auswählen',
                'meta' => array(
//                    'html' => '<form action="" method="post">
//<input type="hidden" name="operation" value="selectLayout"/>
//<input type="hidden" name="old_layout" value="sphinxdoc"/>
//<input type="hidden" name="document_id" value="137"/>
//<select name="selectedlayout">
//<option value="default">Layout1</option>
//<option selected value="sphinxdoc">Layout2</option>
//<option value="agogo">Layout3</option>
//<option value="nature">Layout4</option>
//<option value="scrolls">Layout5</option>
//</select>'
                )
        );
        $wp_admin_bar->add_node($layout_parent);

        $layout1 =  array(
            'id' => 'layout1',
            'title' => 'Layout 1',
            'parent' => 'select_layout',
            'meta' => array()
        );

        $wp_admin_bar->add_node($layout1);
    }

}