<?php

/*
 *
 * Siehe Sphinx/SphinxDocument
 *
 *
 */


/**
 * Class Documents
 */
class Documents {

    /**
     * @var string
     */
    private $dbTableNameDocuments = "dokumummy_documents";
    /**
     * @var string
     */
    private $dbTableNameDocumentInGroup = "dokumummy_documents_in_groups";


    /**
     *
     */
    public function __construct(){
        global $wpdb;

        $this->dbTableNameDocuments = $wpdb->prefix . $this->dbTableNameDocuments;
        $this->dbTableNameDocumentInGroup = $wpdb->prefix . $this->dbTableNameDocumentInGroup;
    }


    /**
     *
     */
    public function getAllDocuments(){
        global $wpdb;

        $documents = $wpdb->get_results("SELECT * FROM  $this->dbTableNameDocuments");
        return $documents;

    }


    public function getDocument($id) {
        global $wpdb;

        $document = $wpdb->get_row("SELECT * FROM $this->dbTableNameDocuments WHERE id=$id");
        return $document;
    }

    /**
     * @param $user_id
     */
    public function getDocumentsCreatedByUser($user_id){
        global $wpdb;

        $documents = $wpdb->get_results("SELECT * FROM $this->dbTableNameDocuments WHERE user_id=$user_id");
        return $documents;
    }


    /**
     * @param $groupId
     */
    public function getDocumentsInGroup($groupId){
        global $wpdb;
        $documents = $wpdb->get_results("SELECT * FROM $this->dbTableNameDocumentInGroup dig
                                  INNER JOIN $this->dbTableNameDocuments d on dig.document_id = d.id
                                  WHERE dig.group_id = $groupId");

        return $documents;
    }

    /**
     * @param $doc_id
     * @param $group_id
     */
    public function removeDocumentFromGroup($doc_id, $group_id){
        global $wpdb;
        $wpdb->delete($this->dbTableNameDocumentInGroup, array(
            'document_id' => $doc_id,
            'group_id' => $group_id
        ));
    }

    public function getAbschnitte($docId) {
        $sphinx = new SphinxDocument("", "", $docId);
        $sphinx->makeHTMLTest();
        return $sphinx->getAbschnitte();
    }

    public function addAbschnitt ($content, $doc_id) {
        $sphinx = new SphinxDocument("", "", $doc_id);
        $sphinx->addAbschnitt($content);
    }

    public function updateAbschnitt($doc_id, $abschnitt_id, $content) {
      $sphinx = new SphinxDocument("", "", $doc_id);
      echo "test -----" . $sphinx->updateAbschnitt($abschnitt_id, $content);
    }


    /**
     * @param $document_id
     */
    public function deleteDocument($document_id){
        global $wpdb;
        $document = $wpdb->get_row("SELECT * FROM $this->dbTableNameDocuments WHERE id=$document_id");

        $sphinx = new SphinxDocument("", "", $document->id);
        $sphinx->deleteDocument();
        $wpdb->delete($this->dbTableNameDocuments, array(
            'id' => $document_id
        ));
    }



    public function createNewDocument($project_name, $authorName, $userId) {
        global $wpdb;

        //$project_path = $sphinx->getProjektPfad();

        if(!$wpdb->insert($this->dbTableNameDocuments, array(
                'name' => $project_name,
                'path' => "",
                'layout' => "",
                'updated_at' => current_time('mysql'),
                'user_id' => $userId
        ))) {
            die("Eintrag in der DB nicht erfolgreich - Models/Documents.php");
        }else{
            $sphinx = new SphinxDocument($project_name, $authorName, $wpdb->insert_id);//die id nicht vergessen!
        }
    }






















	/**
	 * [initDatabase description]
	 * Erstellt Datenbanken für Dokumente
	 * Datenbanken:
	 * Prefix + dokumummy_documents
	 * Prefix + dokumummy_documents_in_groups
	 */
	public function initDatabase() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/**
		 * Datenbank für Dokumente
		 */
		$documents_table = $wpdb->prefix . $this->dbTableNameDocuments;
		$wps_usertable = $wpdb->prefix . "users";

	    $sql = "CREATE TABLE IF NOT EXISTS $documents_table (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			path varchar(255) NOT NULL,
			layout varchar(255) NOT NULL,
			user_id bigint(20) UNSIGNED NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			updated_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			FOREIGN KEY (user_id) references $wps_usertable(ID) on update cascade on delete cascade
	    );";
		dbDelta( $sql );
		
		/**
		 * Datenbanken für Verbindung von Gruppen zu Dokumenten
		 */
		$documents_in_groups_table = $wpdb->prefix . $this->dbTableNameDocumentInGroup;
		$group_table = $wpdb->prefix . "dokumummy_groups";

		$sql = "CREATE TABLE IF NOT EXISTS $documents_in_groups_table (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			document_id int(11) UNSIGNED NOT NULL,
			group_id int(11) UNSIGNED NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			updated_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			FOREIGN KEY (document_id) references $documents_table(id) on update cascade on delete cascade,
			FOREIGN KEY (group_id) references $group_table(id) on update cascade on delete cascade
	    );";
		dbDelta( $sql );

	}
}

?>