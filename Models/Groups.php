<?php
class Groups {

	private $dbTableNameGroup = "dokumummy_groups";
	private $dbTableNameUserInGroup = "dokumummy_users_in_groups";



	public function getAllGroups() {
		global $wpdb;

	    $table_name = $wpdb->prefix . $this->dbTableNameGroup;

	    $results = $wpdb->get_results( 'SELECT * FROM '.$table_name);

	    return $results;
	}


	public function getAuthGroups() {

	}


	/**
	 * [initDatabase description]
	 * Erstellt Datenbanken für die Gruppen
	 * Datenbanken:
	 * Prefix + dokumummy_groups
	 * Prefix + dokumummy_users_in_groups
	 */
	public function initDatabase()
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


		/**
		 * Datenbank für Gruppen
		 */
		$table_name_groups = $wpdb->prefix . $this->dbTableNameGroup;

	    $sql = "CREATE TABLE IF NOT EXISTS $table_name_groups (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			name varchar(255) DEFAULT NULL,
			description varchar(255) DEFAULT NULL,
			active int(1) DEFAULT 1,
			created_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 			updated_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 			PRIMARY KEY (id)
	    )";
		dbDelta( $sql );


	    /**
		 * Datenbank für die Verbindung von User zu Gruppen
		 */
		$table_name_users_in_groups = $wpdb->prefix . $this->dbTableNameUserInGroup ;
		$wps_usertable = $wpdb->prefix . "users";

	    $sql = "CREATE TABLE IF NOT EXISTS $table_name_users_in_groups (
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			group_id int(11) UNSIGNED NOT NULL,
			created_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 			updated_at timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (id),
			FOREIGN KEY (user_id) references $wps_usertable(ID) on update cascade on delete cascade,
			FOREIGN KEY (group_id) references $table_name_groups(id) on update cascade on delete cascade
	    )";
		dbDelta( $sql );


	   	/**
	   	 * Demo Daten hinzufügen
	   	 */
	   	$sql = "INSERT INTO `$table_name_groups`(`name`, `description`) VALUES ('Entwicklung','Hier gehört alles zur Entwicklung rein');";
		dbDelta( $sql );
		$sql = "INSERT INTO `$table_name_groups`(`name`, `description`) VALUES ('Support','Hier gehört alles zum Support rein');";
		dbDelta( $sql );
	}


	private function buildDbName($para) {
		global $wpdb;
		return $wpdb->prefix . $para;
	}
}

?>