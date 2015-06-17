<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 17.06.2015
 * Time: 17:55
 */

class SphinxDocument {

    public function __construct(){

    }

    /**
     * @param  $abschnitt
     */
    public function addAbschnitt($abschnitt){

    }


    public function createNewDocument($path, $project_name, $author){
        $command = "Scripts/createDocument.py ".$path." ".$project_name." ".$author;
        shell_exec($command);
    }


    public function getDocumentContent(){
        return "content";
    }




}