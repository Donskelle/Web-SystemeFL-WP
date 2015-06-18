<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 17.06.2015
 * Time: 17:55
 */

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
    public function __construct($id= ""){
        //TODO: Überprüfe, ob ID schon vorhanden.
    }

    /**
     * @param  DocumentAbschnitt $abschnitt
     */
    public function addAbschnitt($abschnitt){
        $abschnitt ->getFileName();
    }


    /**
     * Ertellt eine neues Sphinxproject
     *
     * @param $project_name
     * @param $author
     */
    public function createNewDocument( $project_name, $author){

        $project_path = $this->sphinxDir."/".$project_name;
        $command = "python ". $this->sphinxScriptCreateDocument ." ".$project_path." ".$project_name." ".$author;
        //TODO: Insert into database.
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
        echo $command;
        $this->changePermissions(); //gibt dem webserver schreib rechte für das neue Projekt.
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
     * @param $project_name
     */
    public function deleteDocument($project_name){
        //TODO: implement
    }


    /**
     *
     * @return array
     */
    public function getAbschnitte(){
        $abschnitte = array();
        return $abschnitte; //TODO: Implement
    }


}