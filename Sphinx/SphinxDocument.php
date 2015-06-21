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

    private $dbDocuments = 'wp_dokumummy_documents';



    /**
     * Speicherordner der Sphinxprojekte.
     * @var
     */
    private $sphinxDir = "/var/www/wordpress/wp-content/plugins/doku_mummy-plugin/Sphinx/SphinxProjects";


    /**
     * Pfad zu createDocument.py
     *
     * @var string
     */
    private $sphinxScriptCreateDocument  = "/var/www/wordpress/wp-content/plugins/doku_mummy-plugin/Sphinx/Scripts/createDocument.py";


    /**
     * Pfad zur rechte.sh
     *
     * @var string
     */
    private $sphinxScriptPermissions = "/var/www/wordpress/wp-content/plugins/doku_mummy-plugin/Sphinx/Scripts/./changePermission.sh";

    /**
     * Erstellt ein SphinxDocument-Objekt.
     *
     * @param string $id
     */
    public function __construct($projectpath){

    }

    /**
     * @param  DocumentAbschnitt $abschnitt
     */
    public function addAbschnitt($abschnitt){
        $abschnitt ->getFileName();
        //TODO: Write to filesystem
    }



    /**
     * Ertellt eine neues Sphinxproject
     *
     * @param $project_name Name des Projektes.
     * @param $authorName Autorname, wahrscheinlich wp nicename.
     * @param $userId Ersteller des Projekts.
     */
    public function createNewDocument( $project_name, $authorName, $userId){
        global $wpdb;
        $project_path = $this->sphinxDir."/".$project_name;
        $command = "python ". $this->sphinxScriptCreateDocument ." ".$project_path." ".$project_name." ".$authorName;


        if(!$wpdb->insert($this->dbDocuments, array(
                'name' => $project_name,
                'path' => $project_path,
                'layout' => "",
                'updated_at' => current_time('mysql'),
                'user_id' => $userId
        ))){
           echo "createNewDocument not successful";
        }else{
            //Erstelle das Projekt nur, wenn der Datenbankeintrag erfolgreich war. Verhindert komische Referenzen etc.
            $output = shell_exec($command);
            echo "<pre>$output</pre>";
            echo $command;
            $this->changePermissions(); //gibt dem webserver schreib rechte für das neue Projekt.
        }
    }


    /**
     * Überprüft ob ein Projekt bereits vorhanden ist.
     *
     * @param $id ProjektID
     * @return bool
     */
    private function isProjectExisting($id){
        global $wpdb;
        $projectName = $wpdb->get_results( "SELECT name FROM  wp_dokumummy_documents WHERE id=".$id);

        return mysql_fetch_row($projectName); //TODO: Asutesten.
    }

    /**
     * Auch die Permission der Parentfolder muss geändert werden, damit Scripte ausgefüht werden können.
     * Führe dies nach jeder Änderung aus.
     */
    private function changePermissions(){
        shell_exec("sudo ".$this->sphinxScriptPermissions);
    }

    /**
     * Löscht ein Dokument
     *
     * @param $project_id Id des Projektes.
     */
    public function deleteDocument($project_path){
        global $wpdb;
        $command = "rm -rf $project_path";
        shell_exec("$command");

    }



    /**
     * Diese Methode liest die Abschnitte des Sphinx-Projektes aus der index.rst und gibt sie als Array von DocumentAbschnit zurück.
     *
     * Wenn der übergebene Parameter nicht stimmt, wird die() ausgelöst.
     *
     * @return array
     */
    public function getAbschnitte($project_path){
        global $wpdb;
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



}