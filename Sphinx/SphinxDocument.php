<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 17.06.2015
 * Time: 17:55
 */

require "DocumentAbschnitt.php";


/**
 * Class SphinxDocument
 *
 * Diese Klasse verwaltet ein Sphinxprojekt (Document).
 *
 *
 */
class SphinxDocument {

    /**
     * Speicherordner der Sphinxprojekte.
     * @var
     */
    private $sphinxDir = "Sphinx/SphinxProjects";


    /**
     * Pfad zu createDocument.py
     *
     * @var string
     */
    private $sphinxScriptCreateDocument = "Sphinx/Scripts/createDocument.py";


    /**
     * Pfad zur rechte.sh
     *
     * @var string
     */
    private $sphinxScriptPermissions = "Sphinx/Scripts/./changePermission.sh";


    /**
     * @var array
     */
    private $aAbschnitteDesDokuments = array();


    /**
     * @var string
     */
    private $sProjectPath = "";

    /**
     * @var bool
     */
    private $documentDeleted = false;

    /**
     * Erlaubt das Erstellen eines neuen , aber auch das Aufrufen eines alten Projektes.
     *
     * Wenn ein projectPath, aber kein anderer Parameter übergeben wird, soll ein existierendes Projekt aufgerufen werden.
     *
     * Wenn projectName, authorName und userId übergeben werden, wird ein neues Projekt angelegt.
     *
     * @param string $projectName
     * @param string $authorName
     * @param string $userId
     * @param string $projectPath
     */
    public function __construct($projectName="", $authorName="", $projectPath = ""){
        $this->sphinxDir = plugin_dir_path( __FILE__ ) . $this->sphinxDir ;
        $this->sphinxScriptCreateDocument = plugin_dir_path( __FILE__ ) . $this->sphinxScriptCreateDocument ;
        $this->sphinxScriptPermissions = plugin_dir_path( __FILE__ ) . $this->sphinxScriptPermissions ;

        $this->sProjectPath = $projectPath;


        if($this->sProjectPath != ""){

            if($this->isProjectExisting($this->sProjectPath)){
                $this->aAbschnitteDesDokuments = $this->extractAbschnitte($this->sProjectPath);
            }else{
                die("corrupted projectPath - SphinxDocument.php");
            }

        }else if($projectName != "" AND $authorName != ""){
            $this->createNewDocument($projectName, $authorName);
        }else{
            die("falscher Parameter - SphinxDocument.php");
        }
    }   

    /**
     *
     * @param  $content string
     */
    public function addAbschnitt($content){
        $this->isUnuseable();
        $abschnitt = new DocumentAbschnitt(("doc".$this->getNewAbschnittFileName()), $content);
        $abschnitt ->getFileName();
        //TODO: Write to filesystem
    }

    private function getNewAbschnittFileName(){
        $newId = 0;

        if(count($this->aAbschnitteDesDokuments) != 0){
            //Der idWert des letzten Abschnittes + 1
            $newId = intval(substr($this->aAbschnitteDesDokuments[count($this->aAbschnitteDesDokuments-1)], 3)) + 1;

        }
        return $newId;
    }

    public function getProjektPfad(){
        return $this->sProjectPath;
    }

    /**
     * Ertellt eine neues Sphinxproject im Filesystem.
     *
     * Wichtig: Keine DB Registrierung an dieser Stelle. Nur ausführen, nachdem das Projekt in der DB erstellt wurde.
     *
     * @param $project_name string Name des Projektes.
     * @param $authorName string Autorname, wahrscheinlich wp nicename. */
    private function createNewDocument( $project_name, $authorName){
        $this->sProjectPath = $this->sphinxDir."/".$project_name;

        $command = "python ". $this->sphinxScriptCreateDocument ." ".$this->sProjectPath." ".$project_name." ".$authorName;

        $output = shell_exec($command);
        $this->changePermissions(); //gibt dem webserver schreib rechte für das neue Projekt.

        //TODO: Dieser Teil soll in Document.php ausgelagert werden.
       /* if(!$wpdb->insert($this->dbDocuments, array(
                'name' => $project_name,
                'path' => $project_path,
                'layout' => "",
                'updated_at' => current_time('mysql'),
                'user_id' => $userId
        ))){
           echo "createNewDocument not successful";
        }else{
            //Erstelle das Projekt nur, wenn der Datenbankeintrag erfolgreich war. Verhindert komische Referenzen etc.

        }*/

        return $this->sProjectPath = $this->sphinxDir."/".$project_name;
    }


    /**
     * Überprüft ob ein Projekt bereits vorhanden ist.
     *
     *
     * @param $projekt_path string Projektpfad.
     * @return bool
     */
    private function isProjectExisting($projekt_path){
        return file_exists($projekt_path."/source/index.rst");
    }

    /**
     * Auch die Permission der Parentfolder muss geändert werden, damit Scripte ausgefüht werden können.
     * Führe dies nach jeder Änderung aus.
     */
    private function changePermissions(){
        shell_exec("sudo ".$this->sphinxScriptPermissions);
    }

    /**
     * Löscht das Dokument dieses Objektes.
     *
     * Wichtig: Das Dokument ist hier nach unbrauchbar.
     *
     * @param $project_id Id des Projektes.
     */
    public function deleteDocument(){
        $this->isUnuseable();
        $command = "rm -rf $this->sProjectPath";
        shell_exec("$command");
        $this->documentDeleted = true;
    }

    /**
     * Wenn das referenzierte Dokument gelöscht wurde, aber das Objekt wieder verwendet wird, wird eine Excepton geworfen.
     *
     * Alle Publicmethods dieser Klasse haben diese Methode im Funktionskörper.
     *
     * @throws Exception 
     */
    private function isUnuseable(){
        if($this->documentDeleted){
            throw new Exception("Das referenzierte Dokument besteht nicht mehr");
        }
    }

    /**
     * Diese Methode liest die Abschnitte des Sphinx-Projektes aus der index.rst und gibt sie als Array von DocumentAbschnit zurück.
     *
     * Wenn der übergebene Parameter nicht stimmt, wird die() ausgelöst.
     *
     * @return array
     */
    private function extractAbschnitte($project_path){
        //Abschnitte definiert in der index.rst unter Contents
        $abschnitte = array();

        $filePath = $project_path."/source/index.rst";

        //Auslesen der Index.rst um an Contents zu kommen.
        $data = file_get_contents($filePath);

        //toctree ist ein Element in der index.rst. Unter Toctree werden die verlinkten Datein aufgeführt.
        $toc_tree = ".. toctree::";
        $toc_tree_pos = strpos($data ,$toc_tree);
        $indices = "Indices"; //das Element unter dem Contentsabschnitt.
        $indices_pos = strpos($data, $indices);

        //Auschneiden von Contents, hat noch andere Elemente.
        $content_with_other_stuff = substr($data, $toc_tree_pos, $indices_pos-$toc_tree_pos);


        //Alle im Contents referenzierten Dateien fangen mit doc1, doc2 usw an.
        $doc_results_array = array();
        preg_match_all("/doc[0-9]+/", $content_with_other_stuff, $doc_results_array); //Pro Treffer wird ein Array mit dem Ergebnis in das Ergebnis array gepushed

        //Reduzieren des Arrays.
        $doc_results = array();
        foreach($doc_results_array as $val ){
            $doc_results[] = $val[0];
        }

        foreach($doc_results as $res){
            $abschnitte[] = new DocumentAbschnitt($res, file_get_contents($this->sphinxDir."/janTest/source/$res".".rst"));
        }

        return $abschnitte;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAbschnitte(){
        $this->isUnuseable();
        return $this->aAbschnitteDesDokuments;
    }

}