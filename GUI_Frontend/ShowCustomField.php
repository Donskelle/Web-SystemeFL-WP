<?php
/**
 * Created for DokuMummy_Plugin.
 * User: Jan
 * Date: 02.06.2015
 * Time: 13:33
 */

class ShowCustomField {
    public function __construct(){
        add_action('the_content', array($this, 'add_function_to_page'));
    }

    public function add_function_to_page($content)
    {
        if ( is_page() )
        {
            $id = get_the_ID();
            $meta = get_post_meta($id, 'custom_element_grid_class_meta_box', true);
            if($meta != null)
            {
                switch ($meta) {
                    case 'Keine':
                        $content .= "Keine";
                        break;
                    case 'Login':
                        $content .= "Login";
                        break;
                    case 'Dokumente':
                        $content .= "Dokumente";
                        break;

                    case 'Gruppen':
                        $content .= "Gruppen";
                        break;

                    default:
                        $content .= "defaultController";
                        break;
                }
            }
        }

        return $content;
    }
}