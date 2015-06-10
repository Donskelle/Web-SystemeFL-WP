<?php
/**
 * Menu Sidebar Widget
 * von Fabian
 */
add_action("plugins_loaded", "widget_sidebar_init");
add_action('wp_enqueue_scripts', 'add_menu_stylesheet' );

function add_menu_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('css/menu.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}


function viewMenu() {
	new Menu();
}

function widget_sidebar_init() {
    if ( !function_exists("wp_register_sidebar_widget") )
        return;


    wp_register_sidebar_widget(
    	"Widget-Menu-DokuMummy", 
    	"Menu DokuMummy",
    	"viewMenu",
    	array(
       		'description' => 'Stellt das Menu von DokuMummy dar.'
    	)
    );
}


class Menu {   
    private $privateDocs;
    private $privateGroups;
    private $currentRoute;



	public function __construct() {
        //$privateDocs = getAuthDocuments();
    	//$privateGroups = getAuthGroups();
       
        $this->privateDocs = array(
            0 => array(
                "name" => "DokuMummy",
                "url" => "#" 
            ),
            1 => array(
                "name" => "Anrufbewältigung",
                "url" => "#" 
            )
        );

        $this->privateGroups = array(
            0 => array(
                "name" => "Entwicklung",
                "url" => "#" 
            ),
            1 => array(
                "name" => "Support",
                "url" => "#" 
            )
        );
    	
    	echo $this->view();
    }

    private function view() {
    	$menu = array();
    	$menu[] = "<div class='menuDokuMummy'>";
    		$menu[] = "<ul>";
    			$menu[] = "<li class='title'>Home</li>";
	    		$menu[] = "<li><a href='Startseite'>Startseite</a></li>";

	    		$menu[] = "<li class='title'>Meine Dokumente</li>";
	    		$menu[] = "<li><a href=''>Neues Dokument</a></li>";
                foreach ($this->privateDocs as $document) {
                    $menu[] = "<li><a href='" . $document["url"] . "'>" . $document["name"] . "</a></li>";
                }


	    		$menu[] = "<li class='title'>Gruppen</li>";
                foreach ($this->privateGroups as $group) {
                    $menu[] = "<li><a href='" . $group["url"] . "'>" . $group["name"] . "</a></li>";
                }
	 
	    	$menu[] = "</ul>";
	    $menu[] = "</div>";
    	return implode("\n", $menu);
    }
}
?>