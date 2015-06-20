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
        //TODO: Select spezifizieren. Man braucht nicht alles
        $documents = $wpdb->get_results("SELECT * FROM $this->dbTableNameDocumentInGroup dig
                                  INNER JOIN $this->dbTableNameDocuments d on dig.document_id = d.id
                                  WHERE dig.group_id = $groupId");
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


    /**
     * @param $document_id
     */
    public function deleteDocument($document_id){
        //TODO: Auch mit Sphinx löschen.
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