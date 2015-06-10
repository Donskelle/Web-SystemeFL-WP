<?php
/**
 * Created for DokuMummy_Plugin.
 * User: Jan
 * Date: 02.06.2015
 * Time: 13:33
 */
function frontendController(){
    new ShowCustomField();
}


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

                    case 'Startseite':
                        $content .= "Willkommen auf DokuMummy Worpress";
                        break;

                    case 'Dokumente':
                        $content .= "Dokumente";
                        break;

                    case 'Gruppen':
                        require_once("Views/GroupView.php");
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