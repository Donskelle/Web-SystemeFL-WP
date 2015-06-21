<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 20.06.2015
 * Time: 13:21
 */


/*TODO: Ansicht eines Dokumentes/Projektes. Siehe Sphinx/SphinxDocument und Models/Documents.php für Model.
 *
 */
new DocumentView();

class DocumentView{



    public function __construct(){
        $doc = new Documents();

        // Neues Dokument erstellen
        if(isset($_POST["operation"]))
        {   
            if($_POST["operation"] == "create") {
                $current_user = wp_get_current_user();
                $doc->createNewDocument($_POST["project_name"], $current_user->display_name, get_current_user_id());
            }
            else if ($_POST["operation"] == "delete") {
                $doc->deleteDocument($_POST["id"]);
            }
            else if($_POST["operation"] == "selectGroup") {
                $group = new Groups();
                $group->selectGroup($_POST["selectedGroup"], $_POST["document_id"]);
            }
        }
        if(isset($_GET["id"]))
        {
            $document = $doc->getDocument($_GET["id"]);
            $this->viewDocument($document);
        }
        else if(isset($_GET["create"])) {
            $this->viewDocumentCreateForm($_GET["create"]);
        }
        else {
           $authDocs = $doc->getDocumentsCreatedByUser(get_current_user_id());

            $this->viewShortDoc($authDocs); 

            $this->viewDocumentCreateForm();
        }
    }

    
    public function viewAddAbschnitt(){
        //TODO: das erstllen eines neuen Abschnitts.
    }


    public function viewGeneratedHtml(){

    }


    public function viewRemoveAbschnitt(){

    }
    
    public function viewDocument($document) {
        $user = wp_get_current_user();
        $output = array();
        $output[] = "<h2>$document->name</h2>";

        if($user->ID == $document->user_id)
        {
            $this->viewDeleteForm($document->id);
            $this->viewFormSelectGroup($document->id);
        }

        echo implode("\n", $output);
    }

    public function viewFormSelectGroup($id) {
        $ouput = array();
        $ouput[] = "<h2>Gruppe zuweisen</h2>";
        $ouput[] = "<form action=\"\" method=\"post\">";
        $ouput[] = '<input type="hidden" name="operation" value="selectGroup"/>';
        $ouput[] = '<input type="hidden" name="document_id" value="' . $id . '"/>';

        $group = new Groups();
        $groups = $group->getDocumentGroups($id);
        print_r($groups);

        $ouput[] = "<select name='selectedGroup'>";
        // keine aktive gruppe
        if($groups["active"] == "") {

            $ouput[] = "<option value=\"none\">Keiner Gruppe zugewiesen</h2>";

            for ($i=0; $i < count($groups["groups"]); $i++) { 
                $ouput[] = "<option value='" . $groups["groups"][$i]->id . "'>" . $groups["groups"][$i]->name . "</option>";
            }
        }
        // aktive gruppe
        else {
            echo $groups["groups"][0]->id;
            $ouput[] = "<option value='none'>Keiner Gruppe zugewiesen</h2>";

            for ($i=0; $i < count($groups["groups"]); $i++) { 
                if($groups["groups"][$i]->id == $groups["active"]->group_id)
                    $ouput[] = "<option selected value='" . $groups["groups"][$i]->id . "'>" . $groups["groups"][$i]->name . "</option>";
                else
                    $ouput[] = "<option value='" . $groups["groups"][$i]->id . "'>" . $groups["groups"][$i]->name . "</option>";
            }
        }   
        $ouput[] = "</select>";

        $ouput[] = '<button type="submit" >Zuweisen</button>';
        $ouput[] = "</form>";
        

        echo implode("\n", $ouput);
    }


    public function viewGeneratedPDF(){
        //TODO: Output, soll im SphinxDocument.php generiert werden
    }

    public function viewShortDoc($documents) {
        $response = array();
        $response[] = '<h2>Deine Dokument</h2>';

        foreach ($documents as $doc) {
            $response[] = "<div>";
            $response[] = "<a href='?id=$doc->id'>$doc->name</a>";
            $response[] = "</div>";
        }

        echo implode("\n", $response);
    }

    public function viewDeleteForm($id) {
        $response = array();
        $response[] = '<form action="./" method="post">';
            $response[] = '<input type="hidden" name="id" value="'.$id.'" placeholder="Dokumentenname" required maxlength="250"/>';
            $response[] = '<input type="hidden" name="operation" value="delete"/>';
            $response[] = '<button type="submit" value="" class="button" >Löschen</button>';
        $response[] = '</form>';
        echo implode("\n", $response);
    }

    public function viewDocumentCreateForm() {
        $response = array();
        $response[] = '<h2>Dokument erstellen</h2>';
        $response[] = '<form action="" method="post">';
            $response[] = '<input type="text" name="project_name" value="" placeholder="Dokumentenname" required maxlength="250"/>';
            $response[] = '<input type="hidden" name="operation" value="create"/>';
            $response[] = '<input type="submit" name="submit" value="Erstellen" class="button" />';
        $response[] = '</form>';
        echo implode("\n", $response);
    }

}