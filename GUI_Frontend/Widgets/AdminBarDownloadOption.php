<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 21:04
 */

class AdminBarDownloadOption {

    private $pdf_link;
    private $zip_link;
    private $doc_name;

    public function __construct($pdf_link, $zip_link,  $doc_name){
        $this->pdf_link = $pdf_link;
        $this->zip_link = $zip_link;
        $this->doc_name = $doc_name;

        add_action('admin_bar_menu', array($this,'showDownloadOptions'), 998); ///998 ist die Priorität
    }


    public function showDownloadOptions($wp_admin_bar){

        $download_parent = array(
            'id' => 'download_parent',
            'title' => 'Download'
        );

        $wp_admin_bar->add_node($download_parent);


        $pdf_option = array(
            'id' => 'pdf_option',
            'title' => '<a href="'.$this->pdf_link.'" class="downloadLink" download="'.$this->name.'.pdf" target="_blank">PDF</a>',
            'parent' => 'download_parent'
        );
        $wp_admin_bar->add_node($pdf_option);

        $zip_option = array(
            'id' => 'zip_option',
            'title' => '<a href="'.$this->zip_link.'" class="downloadLink" target="_blank">Zip</a>',
            'parent' => 'download_parent'
        );
        $wp_admin_bar->add_node($zip_option);
    }

}