<?php


new GroupView();

class GroupView {
	public function __construct() {
		$groups = new Groups();
		$arGroups = $groups->getAllGroups();

        echo $this->view($arGroups);
    }

    private function view($arGroups) {
    	$output = array();

    	$output[] = "<div class='groupView'>";
    	$output[] = "<a href='#'>Gruppe erstellen</a>";

    	if(count($arGroups) > 0) {
    		foreach ( $arGroups as $group ) {
    			$output[] = "<div class='group'>";
    				$output[] = "<p>" . $group->name . "</p>";
    				$output[] = "<p>" . $group->description . "</p>";
    			$output[] = "</div>";
    		}
    	}
    	else {
    		$output[] = "<p>Es wurden keine Gruppen erstellt.</p>";
    	}

    	$output[] = "</div>";

    	return implode("\n", $output);
    }
}
?>