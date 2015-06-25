<?php



new DocumentView();
/**
 * Stellt Dokumenten dar
 */
class DocumentView {

    /**
     * [__construct description]
     * Stellt entsprechend der Anfrage das Dokument dar
     */
    public function __construct(){
        $doc = new Documents();
        // Neues Dokument erstellen
        if(isset($_POST["operation"]))
        {   
            if($_POST["operation"] == "create") {
                $current_user = wp_get_current_user();
                $doc->createNewDocument($this->saveInputs($_POST["project_name"]), $current_user->display_name, get_current_user_id(), $this->saveInputs($_POST["layout"]));
            }
            else if ($_POST["operation"] == "delete") {
                $doc->deleteDocument($this->saveInputs($_POST["id"]));
            }
            else if($_POST["operation"] == "selectGroup") {
                $group = new Groups();
                $group->selectGroup($this->saveInputs($_POST["selectedGroup"]), $this->saveInputs($_POST["document_id"]));
            }
            else if($_POST["operation"] == "addAbschnitt") {
                $doc->addAbschnitt($this->saveInputs($_POST["content"]), $this->saveInputs($_POST["document_id"] ));
            }
            else if($_POST["operation"] == "setContentAbschnitt") {
                $doc->updateAbschnitt($this->saveInputs($_POST["document_id"]), $this->saveInputs($_POST["abschnitt_id"]), $this->saveInputs($_POST["content"]));
            }
            else if($_POST["operation"] == "deleteAbschnitt") {
                $doc->deleteAbschnitt($this->saveInputs($_POST["document_id"]), $this->saveInputs($_POST["abschnitt_id"]));
            }
            else if($_POST["operation"] == "selectLayout")
                $doc->selectLayout($this->saveInputs($_POST["document_id"]), $this->saveInputs($_POST["selectedLayout"]));
        }

        if(isset($_GET["id"]))
        {
            $document = $doc->getDocument($this->saveInputs($_GET["id"]));

            $document->abschnitte = array();
            $document->abschnitte = $doc->getAbschnitte($document->id);

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
    
    /**
     * [viewDocument description]
     * Stellt das übergebene Dokument dar
     * @param  [object] $document [description]
     */
    public function viewDocument($document) {
        $user = wp_get_current_user();

        echo "<h2>$document->name</h2>";
        if($user->ID == $document->user_id)
        {
            $this->viewDeleteForm($document->id);
            $this->viewFormSelectGroup($document->id);
            $this->viewFormSelectLayout($document);
        }
        if($user->ID == $document->user_id || $user->roles[0] == "dokuAdmin" || $user->roles[0] == "administrator" )
        {
            $this->viewAbschnitte($document->abschnitte, $document->id, true);
        }
        else {
            $this->viewAbschnitte($document->abschnitte, $document->id, false);
        }
        $this->viewAddAbschnitt($document->id);
    }


    /**
     * [viewAbschnitte description]
     * Gibt ddie Abschnitte inklusive einer Form aus
     * @param  [array] $abschnitte [description]
     * @param  [int] $doc_id     [description]
     */
    public function viewAbschnitte($abschnitte, $doc_id, $boolAdmin) {
        $output = array();
        $output[] = "<h2>Abschnitte</h2>";
        foreach ($abschnitte as $ab) {
            $output[] = "<div class='abschnitt'>";
            $output[] = "<form action='' method='post'>";
            $output[] = '<input type="hidden" name="document_id" value="' . $doc_id . '"/>';
            $output[] = '<input type="hidden" name="abschnitt_id" value="' . $ab["id"] . '"/>';
            $output[] = '<input type="hidden" name="operation" value="setContentAbschnitt"/>';

            $output[] = "<textarea name='content' rows='15' >" . $ab["content"] . "</textarea>";
            $output[] = "<button type='submit'>Ändern</button>";
            $output[] = "<a target='_blank' href='" . $ab["htmlUrl"] . "'>Ansehen</a>";
            $output[] = "</form>";
            // Wenn Admin können Abschnitte gelöscht werden
            if($boolAdmin) {
                $output[] = "<form action='' method='post'>";
                $output[] = '<input type="hidden" name="document_id" value="' . $doc_id . '"/>';
                $output[] = '<input type="hidden" name="abschnitt_id" value="' . $ab["id"] . '"/>';
                $output[] = '<input type="hidden" name="operation" value="deleteAbschnitt"/>';
                $output[] = "<button type='submit'>Löschen</button>";
                $output[] = "</form>";
            }
            $output[] = "</div>";
        }
        echo implode("\n", $output);
    }

    /**
     * [viewAddAbschnitt description]
     * Gibt eine Form zum Erstellen eines Abschnitts aus
     * @param  [int] $doc_id [description]
     */
    public function viewAddAbschnitt($doc_id){
        $output = array();
        $output[] = "<h2>Abschnitt hinzufügen</h2>";
        $output[] = '<form action="" method="post"  class="abschnitt">';
        $output[] = '<input type="hidden" name="operation" value="addAbschnitt"/>';
        $output[] = '<input type="hidden" name="document_id" value="' . $doc_id . '"/>';
        $output[] = '<textarea name="content" rows="15" value=""></textarea>';
        $output[] = '<button type="submit">Hinzufügen</button>';
        $output[] = '</form>';

        echo implode("\n", $output);
    }


    public function viewFormSelectLayout($doc) {
        $output = array();
        $output[] = "<h2>Layout auswählen</h2>";
        $output[] = "<form action=\"\" method=\"post\">";
        $output[] = '<input type="hidden" name="operation" value="selectLayout"/>';
        $output[] = '<input type="hidden" name="document_id" value="' . $doc->id . '"/>';
        $output[] = "<select name='selectedLayout'>";

        if($doc->layout == "default"){
            $output[] = '<option selected value="default">Layout1</option>';
        }
        else {
            $output[] = '<option value="default">Layout1</option>';
        }

        if($doc->layout == "sphinxdoc") {
            $output[] = '<option selected value="sphinxdoc">Layout2</option>';
        }
        else {
            $output[] = '<option value="sphinxdoc">Layout2</option>';
        }

        if($doc->layout == "agogo") {
            $output[] = '<option selected value="agogo">Layout3</option>';
        }
        else {
            $output[] = '<option value="agogo">Layout3</option>';
        }

        if($doc->layout == "sphinx_rtd_theme") {
            $output[] = '<option selected value="sphinx_rtd_theme">Layout4</option>';
        }
        else{
            $output[] = '<option value="sphinx_rtd_theme">Layout4</option>';
        }

        if($doc->layout == "scrolls") {
            $output[] = '<option selected value="scrolls">Layout5</option>';
        }
        else {
            $output[] = '<option value="scrolls">Layout5</option>';
        }

        $output[] = "</select>";
        $output[] = '<button type="submit" >Layout ändern</button>';
        $output[] = "</form>";
        
        echo implode("\n", $output);
    }

    /**
     * [viewFormSelectGroup description]
     * Form zum Wählen einer Gruppe für ein Dokument darstellen
     */
    public function viewFormSelectGroup($id) {
        $output = array();
        $output[] = "<h2>Gruppe zuweisen</h2>";
        $output[] = "<form action=\"\" method=\"post\">";
        $output[] = '<input type="hidden" name="operation" value="selectGroup"/>';
        $output[] = '<input type="hidden" name="document_id" value="' . $id . '"/>';
        $group = new Groups();
        $groups = $group->getDocumentGroups($id);

        $output[] = "<select name='selectedGroup'>";
        // keine aktive gruppe
        if($groups["active"] == "") {
            $output[] = "<option value=\"none\">Keiner Gruppe zugewiesen</h2>";
            for ($i=0; $i < count($groups["groups"]); $i++) { 
                $output[] = "<option value='" . $groups["groups"][$i]->id . "'>" . $groups["groups"][$i]->name . "</option>";
            }
        }
        // aktive gruppe
        else {
            $output[] = "<option value='none'>Keiner Gruppe zugewiesen</h2>";
            for ($i=0; $i < count($groups["groups"]); $i++) { 
                if($groups["groups"][$i]->id == $groups["active"]->group_id)
                    $output[] = "<option selected value='" . $groups["groups"][$i]->id . "'>" . $groups["groups"][$i]->name . "</option>";
                else
                    $output[] = "<option value='" . $groups["groups"][$i]->id . "'>" . $groups["groups"][$i]->name . "</option>";
            }
        }   
        $output[] = "</select>";
        $output[] = '<button type="submit" >Zuweisen</button>';
        $output[] = "</form>";
        
        echo implode("\n", $output);
    }


    /**
     * [viewShortDoc description]
     * Gibt eine Übersicht der übergebenen Dokumente aus
     * @param  [array] $documents [description]
     */
    public function viewShortDoc($documents) {
        $output = array();
        $output[] = '<h2>Deine Dokument</h2>';
        foreach ($documents as $doc) {
            $output[] = "<div>";
            $output[] = "<a href='?id=$doc->id'>$doc->name</a>";
            $output[] = "</div>";
        }
        echo implode("\n", $output);
    }


    /**
     * [viewDeleteForm description]
     * Stellt einen Button zum Löschen des Dokuments dar
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function viewDeleteForm($id) {
        $output = array();
        $output[] = '<form action="./" method="post">';
            $output[] = '<input type="hidden" name="id" value="'.$id.'" placeholder="Dokumentenname" required maxlength="250"/>';
            $output[] = '<input type="hidden" name="operation" value="delete"/>';
            $output[] = '<button type="submit" value="" class="button" >Löschen</button>';
        $output[] = '</form>';
        echo implode("\n", $output);
    }


    /**
     * [viewDocumentCreateForm description]
     * Gibt eine Form zum Erstellen eines Dokuments aus
     */
    public function viewDocumentCreateForm() {
        $output = array();
        $output[] = '<h2>Dokument erstellen</h2>';
        $output[] = '<form action="./" method="post">';
            $output[] = '<input type="text" name="project_name" value="" placeholder="Dokumentenname" required maxlength="250"/>';
            $output[] = '<select class="form-control" name="layout">';
                $output[] = '<option value="default">Layout1</option>';
                $output[] = '<option value="sphinxdoc">Layout2</option>';
                $output[] = '<option value="agogo">Layout3</option>';
                $output[] = '<option value="sphinx_rtd_theme">Layout4</option>';
                $output[] = '<option value="scrolls">Layout5</option>';
            $output[] = '</select>';
            $output[] = '<input type="hidden" name="operation" value="create"/>';
            $output[] = '<input type="submit" name="submit" value="Erstellen" class="button" />';
        $output[] = '</form>';
        echo implode("\n", $output);
    }


    /**
     * [saveInputs description]
     * Escaped des übergebenen String
     * @param  [string] $str [description]
     * @return [string]      [description]
     * Sicherer String
     */
    public function saveInputs($str) {
        $str = stripslashes($str);
        $str = strip_tags($str);
        $str = esc_sql($str);
        return $str;
    }
}