<?php
/**
 * Created for doku_mummy-plugin.
 * User: Jan
 * Date: 26.06.2015
 * Time: 15:48
 */

class AdminBarDeleteButton {
    private $document_id;


//nur im Dokument view starten.
    public function __construct($doc_id){
        $this->document_id = $doc_id;
        add_action('admin_bar_menu', array($this,'addDelete'),998); ///999 ist die Priorität
    }


    public function addDelete($wp_admin_bar){

        $delete = array(
            'id'    => 'delete_document',
            'title' => 'Dokument löschen',
            'meta'  => array(
            )
        );
        $wp_admin_bar -> add_node($delete);


        $reallyDelete = array(
            'id' => 'really_delete',
            'title'=> '<form method="post" action="./">
<input type="hidden" maxlength="250" value='.$this->document_id.' name="id">
<input type="hidden" value="delete" name="operation">
<button class="button" value="" type="submit">Klicke hier zum Löschen!</button>
</form>',
            'parent' => 'delete_document'
        );

        $wp_admin_bar->add_node($reallyDelete);
    }
}