<?php
class Groups {

	private $dbTableNameGroup = "dokumummy_groups";
	private $dbTableNameUserInGroup = "dokumummy_users_in_groups";



	public function getAllGroups() {
		global $wpdb;

	    $table_name = $this->buildDbName( $this->dbTableNameGroup );

	    $results = $wpdb->get_results( 'SELECT * FROM '.$table_name);

	    return $results;
	}


	public function getAuthGroups() {

	}


	/**
	 * [initGroupDatabase description]
	 * Erstellt Datenbanken für die Gruppen
	 * Datenbanken:
	 * Prefix + DokuMummy_Groups
	 * Prefix + DokuMummy_Users_in_Groups
	 */
	public function initGroupDatabase()
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


		/**
		 * Datenbank für Gruppen
		 */
		$table_name_groups = $this->buildDbName( $this->dbTableNameGroup );

	    $sql = "CREATE TABLE $table_name_groups (
			id int(11) NOT NULL AUTO_INCREMENT,
			name varchar(255) DEFAULT NULL,
			description varchar(255) DEFAULT NULL,
			active int(1) DEFAULT 1,
			PRIMARY KEY id (id)
	    )";
		dbDelta( $sql );


	    /**
		 * Datenbank für die Verbindung von User zu Gruppen
		 */
		$table_name_users_in_groups = $this->buildDbName( $this->dbTableNameUserInGroup );
		$wps_usertable = $wpdb->prefix . "users";

	    $sql = "CREATE TABLE $table_name_users_in_groups (
			id int(11) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) UNSIGNED NOT NULL,
			group_id int(11) NOT NULL,
			UNIQUE KEY id (id),
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