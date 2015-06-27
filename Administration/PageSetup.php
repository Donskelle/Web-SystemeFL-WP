<?php
class PageSetup {
    public function __construct(){
        $this->createPages();
    }

    private function createPages(){
        $startseite = array(
            'post_title' => 'Startseite',
            'post_content' => 'Willkommen zu DokuMummy.',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1
        );

        $dokummente = array(
            'post_title' => 'Dokummente',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1
        );

        $gruppen = array(
            'post_title' => 'Gruppen',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1
        );

        $post_id = wp_insert_post($startseite);
        add_post_meta($post_id, 'custom_element_grid_class_meta_box', 'Startseite');

        $post_id = wp_insert_post($dokummente);
        add_post_meta($post_id, 'custom_element_grid_class_meta_box', 'Dokumente');

        $post_id = wp_insert_post($gruppen);
        add_post_meta($post_id, 'custom_element_grid_class_meta_box', 'Gruppen');


    }
}