<?php


new GroupView();

class GroupView {

	public function __construct() {
		$groups = new Groups();

        /**
         * Wenn gepostet wurde, wird eine Gruppe erstellt
         */
        if($_POST) {
            if(isset($_POST["group_name"]))
                $groups->saveGroup($_POST["group_name"], $_POST["group_description"], get_current_user_id());
        }
        
        // Bestimmte ID wird abgefragt
        if(isset($_GET["id"]))
        {
            $detailGroup = $groups->getGroupAndUsers($_GET["id"]);
            echo $this->detailView($detailGroup);
        }
        // Allgemeine Gruppenansicht
        else {
            $arGroups = $groups->getAuthGroups();
            echo $this->groupView($arGroups);
        }
    }


    private function groupView($arGroups) {
    	$output = array();
        $user = wp_get_current_user();

    	$output[] = "<div class='groupView'>";

        /**
         * Wenn Berechtigungen wird die erstell Form dargestellt.
         */
        if($user->roles[0] == "dokuAdmin" || $user->roles[0] == "administrator" )
        {
            $output[] = '<h2>Gruppe erstellen</h2>';
            $output[] = $this->viewFormCreateGroup();
        }



    	if(count($arGroups) > 0) {
            $output[] = '<h2>Aktive Gruppen</h2>';
    		foreach ( $arGroups as $group ) {
    			$output[] = "<div class='group'>";
    				$output[] = "<p class='groupName'><a href='" . $_SERVER["REQUEST_URI"] . "?id=" .  $group->id . "'>" . $group->name . "</a></p>";
    				$output[] = "<p>" . $group->description . "</p>";
    			$output[] = "</div>";
    		}
    	}
    	else {
            if($user->roles[0] == "dokuAdmin" || $user->roles[0] == "administrator" )
            {
                $output[] = "<p>Es wurden keine Gruppen erstellt.</p>";
            }
            else {
                $output[] = "<p>Sie sind in keiner Gruppe</p>";
            }
    	}

    	$output[] = "</div>";

    	return implode("\n", $output);
    }

    private function detailView($arDetailGroup) {
        $output = array();
        $user = wp_get_current_user();

        $output[] = "<div class='groupView'>";
            $output[] = "<h2>" . $arDetailGroup->name . "</h2>";

            if($arDetailGroup->description != "" && $arDetailGroup->description != null)
                $output[] = "<p>" . $arDetailGroup->description . "</p>";

            $output[] = "<div class='users'>";
            $output[] = "<h2>Benutzer von " . $arDetailGroup->name . "</h2>";
            
            foreach($arDetailGroup->user as $user) {
                $output[] = "<p>";
                $output[] = $user->user_nicename;
                $output[] = "</p>";
            }

            $output[] = "</div>";
        $output[] = "</div>";

        return implode("\n", $output);
    }

    private function viewFormCreateGroup() {
        $response = array();
        $response[] = '<form action="" method="post">';
            $response[] = '<input type="text" name="group_name" value="" placeholder="Gruppenname" required maxlength="250"/>';
            $response[] = '<input type="text" name="group_description" Placeholder="Beschreibung" maxlength="250"/>';
            $response[] = '<input type="submit" name="submit" value="Erstellen" class="button" />';
        $response[] = '</form>';
        return implode("\n", $response);
    }
}
?>