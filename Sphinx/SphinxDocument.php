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
     * Gibt die Namen der Dokumente eines Users aus.
     * @param $userid
     */
    public function getDocumentsByUser($userid){

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
    public function deleteDocument($project_id){
        global $wpdb;

        $result = $wpdb->get_row("SELECT name, path FROM $this->dbDocuments WHERE id=$project_id");

        if(!$result){
            //Wenn das Dokument nicht gefunden wurde.
        }else {
            $wpdb->delete($this->dbDocuments,array(
                'id'=>$project_id
            ));

            $command = "rm -rf $result->path";
            shell_exec("$command");

        }




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