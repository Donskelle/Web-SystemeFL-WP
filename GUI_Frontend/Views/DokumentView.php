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
        if(isset($_POST["project_name"]))
        {
            $current_user = wp_get_current_user();
            $doc->createNewDocument($_POST["project_name"], $current_user->display_name, get_current_user_id());

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
        print_r($document);
    }


    public function viewGeneratedPDF(){
        //TODO: Output, soll im SphinxDocument.php generiert werden
    }

    public function viewShortDoc($documents) {
        $response = array();
        $response[] = '<h2>Deine Dokument</h2>';

        foreach ($documents as $doc) {
            $response[] = "<div>";
            $response[] = $doc->name;
            $response[] = "</div>";
        }

        echo implode("\n", $response);
    }


    public function viewDocumentCreateForm() {
        $response = array();
        $response[] = '<h2>Dokument erstellen</h2>';
        $response[] = '<form action="" method="post">';
            $response[] = '<input type="text" name="project_name" value="" placeholder="Dokumentenname" required maxlength="250"/>';
            $response[] = '<input type="submit" name="submit" value="Erstellen" class="button" />';
        $response[] = '</form>';
        echo implode("\n", $response);
    }

}