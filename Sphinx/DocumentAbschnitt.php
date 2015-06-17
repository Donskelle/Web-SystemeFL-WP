<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 17.06.2015
 * Time: 17:56
 */

class DocumentAbschnitt {

    /**
     * TODO: Es steht noch nicht fest, was genau die ID ist. Vllt die Nummer der rst-File.
     * @var
     */
    private $abschnittId;

    /**
     * Inhalt des Abschnitts.
     *
     * @var string
     */
    private $sAbschnittContent = "";

    public function __construct($content = null){
        //Wenn der Abschnitt nicht null ist, wird er gerade erstellt.
        if($content != null){
            $this->updateAbschnitt($content);
        }
    }

    /**
     * @param $content
     */
    public function updateAbschnitt($content){

    }

    public function getId(){
        return $this->abschnittId;
    }

}