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
 */
class DocumentAbschnitt {

    private $fileName;
    private $abschnittContent;

    public function __construct($filename, $abschnitt_content){
        $this->fileName = $filename;
        $this->abschnittContent = $abschnitt_content;
    }

    /**
     *
     *
     * @return string Name der Datei, die den Abschnitt enthält.
     */
    public function getFileName(){
        return $this->fileName;
    }

    /**
     *
     * @return mixed
     */
    public function getAbschnittContent(){
        return $this->abschnittContent;
    }


    /**
     * @param $content
     */
    public function setAbschnittContent($content){
        $this->fileContent = $content;
    }





}