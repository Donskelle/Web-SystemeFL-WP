<?php

/*
 *
 * Siehe Sphinx/SphinxDocument
 *
 *
 */


class Documents {

	private $dbTableNameDocuments = "dokumummy_documents";
	private $dbTableNameDocumentInGroup = "dokumummy_documents_in_groups";


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