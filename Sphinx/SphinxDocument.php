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
    private $sphinxDirPath = "SphinxProjects";


    /**
     * Pfad zu createDocument.py
     *
     * @var string
     */
    private $sphinxScriptCreateDocumentPath = "Scripts/createDocument.py";


    /**
     * Pfad zur rechte.sh
     *
     * @var string
     */
    private $sphinxScriptPermissionsPath = "Scripts/./changePermission.sh";


    /**
     * @var array
     */
    private $aAbschnitteDesDokuments = array();


    /**
     * Der Pfad zum Projektordner
     * @var string
     */
    private $sProjectPath = "";

    /**
     * @var bool
     */
    private $documentDeleted = false;

    /**
     * Die Id des Projektes in der Datenbank
     * @var string
     */
    private $sProjectId = "";

    /**
     * Counter für die Abschnitte des Dokumentes
     * @var int
     */
    private $internalAbschnittCounter = 0;

    /**
     * Erlaubt das Erstellen eines neuen , aber auch das Aufrufen eines alten Projektes.
     *
     * Wenn ein projectId, aber kein anderer Parameter übergeben wird, soll ein existierendes Projekt aufgerufen werden.
     *
     * Wenn projectName, authorName und projectId übergeben werden, wird ein neues Projekt angelegt.
     *
     * @param string $projectName
     * @param string $authorName
     * @param string $projectId
     */
    public function __construct($projectName="", $authorName="", $projectId = ""){ //TODO: Change to ID.
        $this->sphinxDirPath = plugin_dir_path( __FILE__ ) . $this->sphinxDirPath ;
        $this->sphinxScriptCreateDocumentPath = plugin_dir_path( __FILE__ ) . $this->sphinxScriptCreateDocumentPath ;
        $this->sphinxScriptPermissionsPath = plugin_dir_path( __FILE__ ) . $this->sphinxScriptPermissionsPath ;


        //Felder mit neuen Werten versorgen.
        $this->sProjectId = intval($projectId);
        $this->sProjectPath = $this->sphinxDirPath."/".$this->sProjectId;

        if($projectName !== "" AND $authorName !== "") {
            $this->createNewDocument($projectName, $authorName);
        }else {
            $this->sProjectPath = $this->sProjectPath."/".$this->extractProjectName($this->sProjectPath);
            if ($this->isProjectExisting($this->sProjectPath)) {
                $this->aAbschnitteDesDokuments = $this->extractAbschnitte($this->sProjectPath);
            } else {
                die("corrupted projectPath - SphinxDocument.php: $this->sProjectPath");
            }
        }
    }

    /**
     * Erzeugt einen neuen Abschnitt.
     *
     * Erzeugt ein Abschnittobjekt, schreibt es ins Filesystem, updatet die index.rst und hängt die Abschnitt ans interne Abschnittarray.
     *
     * @param  $content string
     * @return string Die Id des Abschnittes.
     */
    public function addAbschnitt($content){
        $this->isUnuseable();

        $id = intval($this->getNewAbschnittFileName());
        $abschnitt = new DocumentAbschnitt("doc".$id, $content, $id);
        $this->buildAbschnittFile($abschnitt);
        $this->addAbschnittToIndex($abschnitt);
        $this->makeHTML();
        return $abschnitt->getAbschnittId();
    }

    /**
     * @param $abschnittId
     * @throws Exception
     */
    public function removeAbschnitt($abschnittId){
        $this->isUnuseable();
        $abschnitt = null;
        echo "<pre>";
        print_r($this->aAbschnitteDesDokuments);
        echo "</pre>";
        foreach($this->aAbschnitteDesDokuments as $ab){
            if($ab->getAbschnittId() == $abschnittId){
                $abschnitt = $ab;
                break;
            }else{
                die("Falsche Abschnitt ID - SphinxDocuments.php");
            }
        }
        $this->removeAbschnittFromIndexFile($abschnitt);
        $this->deleteAbschnittFromFS($abschnitt);
        echo "<pre>";
        print_r($this->aAbschnitteDesDokuments);
        echo "</pre>";
        $this->makeHTML();
    }


    /**
     * @return mixed
     */
    private function extractProjectName(){
        $scanDir = array_diff(scandir($this->sProjectPath), array(".", "..")); //scandir gibt auch die verzeichnisse "." und ".." zurück. Diese müssen entfernt werden.
        return array_pop($scanDir); //das letzte u. einzige Element ist der Verzeichnisname.
    }


    /**
     * Updatet einen Abschnitt.
     *
     *
     * @param $abschnittId string Die Id des Abschnitts.
     * @param $content
     * @return bool Wurde ein Abschnitt gefunden und geupdated?
     * @throws Exception
     */
    public function updateAbschnitt($abschnittId, $content){
        $this->isUnuseable();

        //wurde ein Abschnitt geupdated?
        $bUpdated = false;
        //Der Abschnitt, der upgedated werden soll.
        $updateAbschnitt = null;
        foreach($this->aAbschnitteDesDokuments as $abschnitt){
            if($abschnitt->getAbschnittId() == $abschnittId){
                $abschnitt->setAbschnittContent($content);
                $this->buildAbschnittFile($abschnitt);
                $bUpdated = true;
                break;
            }
        }
        $this->makeHTML();
        return $bUpdated;
    }


    /**
     * Fügt einen Abschnitt dem Dokument zu.
     *
     * Fügt es dem Objekt und der index.rst zu.
     *
     * @param $abschnitt DocumentAbschnitt
     */
    private function addAbschnittToIndex($abschnitt){
        $indexContent = file_get_contents($this->sProjectPath."/source/index.rst");

        $search_string = "   :maxdepth: 2".PHP_EOL; //Enspricht dem Keyword unter toctree in der index.rst
        foreach($this->aAbschnitteDesDokuments as $ab){
            $search_string .= PHP_EOL."   ".$ab->getFileName();
        }

        $replace_str = $search_string . PHP_EOL . "   " . $abschnitt->getFileName().PHP_EOL;


        $this->aAbschnitteDesDokuments[]=$abschnitt;//Füge den Abschnitt dem internen Verzeichnis zu.

        $str = preg_replace("/$search_string/s", $replace_str, $indexContent);
        file_put_contents($this->sProjectPath."/source/index.rst", $str);
    }


    /**
     *  Entfernt einen Abchnitt von dem Dokument.
     *
     *  Entfern von dem Objekt und der index.rst.
     *
     * @param $abschnitt DocumentAbschnitt
     */
    private function removeAbschnittFromIndexFile($abschnitt){
        //Der erste Teil ist wie addAbschnittToIndexFile. Zuerst wird das alte Abschnittsverzeichnis aufgebaut.
        $indexContent = file_get_contents($this->sProjectPath."/source/index.rst");

        $search_string = "   :maxdepth: 2".PHP_EOL.PHP_EOL; //Enspricht dem Keyword unter toctree in der index.rst
        $replace_string = $search_string;

        foreach($this->aAbschnitteDesDokuments as $ab){
            $search_string .="   ".$ab->getFileName().PHP_EOL;
        } //search_string hat jetzt alle Abschnitte als Einträge.

        echo "<pre>RemoveAbschnitt - searchstinrg: $search_string";

        //Entferne $abschnitt vom Verzeichnis.
        $tmp_arr = [];
        foreach($this->aAbschnitteDesDokuments as $ab){
            if($ab->getAbschnittId() != $abschnitt->getAbschnittId()){
                $tmp_arr[]=$ab;
            }
        }
        $this->aAbschnitteDesDokuments = $tmp_arr;

        //Baue den replace_str auf
        foreach($this->aAbschnitteDesDokuments as $ab){
            $replace_string .=PHP_EOL."   ".$ab->getFileName();
        } //replace_str hat jetzt alle aktuellen Abschnitte als Einträge.

        echo "<br>replacestring: $replace_string</pre>";

        $str = preg_replace("/$search_string/s", $replace_string, $indexContent);
        file_put_contents($this->sProjectPath."/source/index.rst", $str);

    }


    /**
     * Schreibt den Inhalt des Abschnittes in eine Datei mit dem Filenamen, der im Abschnitt gespeichert ist.
     *
     * Die File liegt im source-Ordner des Projektes.
     *
     * @param $abschnitt DocumentAbschnitt
     */
    private function buildAbschnittFile($abschnitt){
        $abschnittFile = fopen($this->sProjectPath."/source/".$abschnitt->getFileName().".rst", 'w');
        fwrite($abschnittFile, $abschnitt->getAbschnittContent());
        fclose($abschnittFile);
    }

    /**
     * @return int
     */
    private function getNewAbschnittFileName(){
        $newId = 0;

        if(count($this->aAbschnitteDesDokuments) != 0){
            //Der idWert des letzten Abschnittes + 1
            $newId = intval($this->aAbschnitteDesDokuments[count($this->aAbschnitteDesDokuments) -1]->getAbschnittId()) + 1;
        }
        return $newId;
    }

    /**
     * Erzeugt eine AbschnittId.
     *
     * Diese Id ist im jeweiligen Abschnitt gespeichert und soll an den Client geschickt werden.
     *
     * @return int
     */
    private function generateAbschnittId(){
        $tmp = $this->internalAbschnittCounter;
        $this->internalAbschnittCounter++;
        return $tmp;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getProjektPfad(){
        $this->isUnuseable();
        return $this->sProjectPath;
    }

    /**
     * Ertellt eine neues Sphinxproject im Filesystem und legt einen ersten Abschnitt an.
     *
     * Wichtig: Keine DB Registrierung an dieser Stelle. Nur ausführen, nachdem das Projekt in der DB erstellt wurde.
     *
     * @param $project_name string Name des Projektes.
     * @param $authorName string Autorname, wahrscheinlich wp nicename. */
    private function createNewDocument( $project_name, $authorName){

        //Erstellt das Verzeichnis basierend auf der im construktor übergebenen ID.
        $res = mkdir($this->sphinxDirPath."/".$this->sProjectId);
        if(!$res){
            die("Verzeichnis nicht erstellt - SphinxDocument.php");
        }
        //var/www/wordpress/...../Sphinx/SphinxProjects/id/projectName
        $this->sProjectPath = $this->sphinxDirPath."/".$this->sProjectId."/".$project_name;

        $command = "python ". $this->sphinxScriptCreateDocumentPath ." ".$this->sProjectPath." ".$project_name." ".$authorName;

        $output = shell_exec($command);

        //Erstelle den ersten Abschnitt.
        $this->addAbschnitt("h1".PHP_EOL."==");

        $this->changePermissions(); //gibt dem webserver schreib rechte für das neue Projekt.
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
        shell_exec("sudo ".$this->sphinxScriptPermissionsPath);
    }


    /**
     * Erstellt das zugehörige HTML in projectid/name/build/html
     */
    private function makeHTML(){
        shell_exec("cd $this->sProjectPath && make html");
    }

    /**
     * Erstellt das zugehörige PDF in projectid/name/build/latex
     */
    private function makePDF(){
        shell_exec("cd $this->sProjectPath && make PDF");
    }


    private function getHTMLPath($abschnittId){
        return $this->sProjectPath."/build/html/doc$abschnittId.html";
    }

    public function getHTML($abschnitt_id){
        //TODO implement
        return $this->getHTMLPath($abschnitt_id);
    }

    public function getPDF(){
        //TODO implement
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
        $toc_tree = ".. toctree::".PHP_EOL.PHP_EOL;
        $toc_tree_pos = strpos($data ,$toc_tree);
        $indices = "Indices"; //das Element unter dem Contentsabschnitt.
        $indices_pos = strpos($data, $indices);

        //Auschneiden von Contents, hat noch andere Elemente.
        $content_with_other_stuff = substr($data, $toc_tree_pos, $indices_pos - $toc_tree_pos);


        //Alle im Contents referenzierten Dateien fangen mit doc1, doc2 usw an.
        $doc_results_array = array();
        preg_match_all("/doc[0-9]+/", $content_with_other_stuff, $doc_results_array); //Pro Treffer wird ein Array mit dem Ergebnis in das Ergebnis array gepushed


        //Reduzieren des Arrays.
        $doc_results = array();
        if(count($doc_results_array) > 0){ //gibt ein leeres array im array zurück. Die Länge zählt als 1.
            foreach($doc_results_array[0] as $val ){
                $doc_results[] = $val;
            }
        }
        //Erzeugen des Abschnittarrayss
        foreach($doc_results as $res){
            $abschnitte[] = new DocumentAbschnitt($res, file_get_contents($this->sProjectPath."/source/$res".".rst"), $this->generateAbschnittId());
        }

        return $abschnitte;
    }

    private function deleteAbschnittFromFS($abschnitt){

        $output = shell_exec("sudo rm ".$this->sProjectPath."/source/".$abschnitt->getFileName().".rst");
        echo "sudo rm ".$this->sProjectPath."/source/".$abschnitt->getFileName().".rst";
        echo $output;
    }
    /**
     * @return array
     * @throws Exception
     */
    public function getAbschnitte(){
        $this->isUnuseable();

        $abschnitteContent = [];

        foreach($this->aAbschnitteDesDokuments as $ab){
            $abschnitteContent[] = array(
                "id" => $ab->getAbschnittId(),
                "filename" => $ab->getFileName(),
                "content" => $ab->getAbschnittContent()
            );
        }

        return $abschnitteContent;
    }


}