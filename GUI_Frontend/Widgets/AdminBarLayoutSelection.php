<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 18:02
 */

class AdminBarLayoutSelection {
    private $old_layout;
    private $doc_id;


    public function __construct($doc_id, $oldLayout){
        $this->doc_id = $doc_id;
        $this->old_layout = $oldLayout;
        add_action('admin_bar_menu', array($this,'showLayoutSelection'), 997); ///998 ist die Priorität
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
            'title' => $this->generateTitle('Layout 1', 'default'),
            'parent' => 'select_layout',
            'meta' => array()
        );

        $wp_admin_bar->add_node($layout1);

        $layout2 =  array(
            'id' => 'layout2',
            'title' => $this->generateTitle('Layout 2', 'sphinxdoc'),
            'parent' => 'select_layout',
            'meta' => array()
        );

        $wp_admin_bar->add_node($layout2);

        $layout3 =  array(
            'id' => 'layout3',
            'title' => $this->generateTitle('Layout 3', 'agogo'),
            'parent' => 'select_layout',
            'meta' => array()
        );

        $wp_admin_bar->add_node($layout3);

        $layout4 =  array(
            'id' => 'layout4',
            'title' => $this->generateTitle('Layout 4', 'nature'),
            'parent' => 'select_layout',
            'meta' => array()
        );

        $wp_admin_bar->add_node($layout4);

        $layout5 =  array(
            'id' => 'layout5',
            'title' => $this->generateTitle('Layout 5', 'scrolls'),
            'parent' => 'select_layout',
            'meta' => array()
        );

        $wp_admin_bar->add_node($layout5);
    }


    private function generateTitle($title, $layoutValue){
        $content = '<form action="" method="post">
<input type="hidden" name="operation" value="selectLayout"/>
<input type="hidden" name="old_layout" value="'.$this->old_layout.'"/>
<input type="hidden" name="document_id" value="'.$this->doc_id.'"/>
<input type="hidden" name="selectedLayout" value="'.$layoutValue.'">
                <button type="submit" >'.$title.'</button></form>';
        return $content;
    }

}