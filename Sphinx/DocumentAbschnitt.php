<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 18.06.2015
 * Time: 17:50
 */


/**
 *
 * Class DocumentAbschnitt
 *
 *
 * Stellt einen Abschnitt eines Sphinxprojekts dar.
 * TODO: IMplement
 */
class DocumentAbschnitt {

    private $fileName;
    private $fileContent;

    public function __construct($filename, $filecontent){
        $this->fileName = $filename;
        $this->fileContent = $filecontent;
    }

    public function getFileName(){
        return $this->fileName;
    }

    public function getFileContent(){
        return $this->fileContent;
    }




}