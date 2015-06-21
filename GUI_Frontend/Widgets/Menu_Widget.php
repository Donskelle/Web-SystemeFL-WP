<?php
/**
 * Menu Sidebar Widget
 * von Fabian
 */
add_action("plugins_loaded", "widget_sidebar_init");
add_action('wp_enqueue_scripts', 'add_menu_stylesheet' );

function add_menu_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'dm-menu-style', plugins_url('css/menu.css', __FILE__) );
    wp_enqueue_style( 'dm-menu-style' );
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
    private $authDocs;
    private $authGroups;
    private $currentRoute;



	public function __construct() {
        /**
         * DEMO DATEN
         * @var array
         */
        $docs = new Documents();
        $this->authDocs = $docs->getDocumentsCreatedByUser(get_current_user_id());


        $groups = new Groups();
        $this->authGroups = $groups->getAuthGroups();


        /**
         * SeitenUrl holen
         * @var array
         */
        $pagesFilter = array(
            'post_type' => 'page',
            'meta_key' => 'custom_element_grid_class_meta_box',
            'meta_value' => 'Gruppen'
        );
        
        $pages = get_posts($pagesFilter);
        $GroupLink = get_permalink($pages[0]->ID);

        $pagesFilter["meta_value"] = "Dokumente";
        $pages = get_posts($pagesFilter);
        $documentLink = get_permalink($pages[0]->ID);


        $pagesFilter["meta_value"] = "Startseite";
        $pages = get_posts($pagesFilter);
        $homeLink = get_permalink($pages[0]->ID);
        
    	
    	echo $this->view($GroupLink, $documentLink, $homeLink);
    }

    private function view($groupLink, $documentLink, $homeLink) {
    	$menu = array();
    	$menu[] = "<div class='menuDokuMummy'>";
    		$menu[] = "<ul>";
    			$menu[] = "<li class='title'><a href='" . $homeLink . "'Home</li>";
	    		$menu[] = "<li><a href='" . $homeLink . "'>Startseite</a></li>";

	    		$menu[] = "<li class='title'><a href='" . $documentLink . "'>Meine Dokumente</li>";
	    		$menu[] = "<li><a href='" . $documentLink . "'>Neues Dokument</a></li>";

                foreach ($this->authDocs as $document) {
                    $menu[] = "<li><a href='" .  $documentLink . "?id=" .  $document->id . "'>" . $document->name . "</a></li>";
                }


	    		$menu[] = "<li class='title'><a href='" . $groupLink . "'>Gruppen</a></li>";
                foreach ($this->authGroups as $group) {
                    $menu[] = "<li><a href='" .  $groupLink . "?id=" . $group->id . "'>" . $group->name . "</a></li>";
                }
	 
	    	$menu[] = "</ul>";
	    $menu[] = "</div>";
    	return implode("\n", $menu);
    }
}
?>