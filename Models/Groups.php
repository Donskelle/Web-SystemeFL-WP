<?php
class Groups {

	private $dbTableGroup = "dokumummy_groups";
	private $dbTableUserInGroup = "dokumummy_users_in_groups";


	function __construct() {
		global $wpdb;

		$this->dbTableGroup = $wpdb->prefix . $this->dbTableGroup;
		$this->dbTableUserInGroup = $wpdb->prefix . $this->dbTableUserInGroup;
	}

    /**
     * @return mixed Alle vorhandenen Gruppen.
     */
	public function getAllGroups() {
		global $wpdb;

	    $results = $wpdb->get_results( 'SELECT * FROM ' . $this->dbTableGroup);

	    return $results;
	}



	public function getAuthGroups() {
		$user = wp_get_current_user();

		if($user->roles[0] == "dokuAdmin" || $user->roles[0] == "administrator") {
			return $this->getAllGroups();
		}
		else {
			return $this->getUserGroups($user->ID);//TODO Methode nicht vorhanden?
		}
	}

    /**
     * Fügt einen Nutzer einer Gruppe hinzu.
     * @param $group_id
     * @param $user_id
     */
	public function addUser($group_id, $user_id) {
		global $wpdb;
		$sql = $wpdb->insert(
			$this->dbTableUserInGroup, 
			array( 
				'user_id' => $user_id, 
				'group_id' =>  $group_id
			)
		);
	}

    /**
     * Löscht einen Benutzer aus einer Gruppe.
     *
     * @param $group_id
     * @param $user_id
     */
	public function deleteUser($group_id, $user_id) {
		global $wpdb;
		$sql = $wpdb->delete(
			$this->dbTableUserInGroup, 
			array( 
				'user_id' => $user_id, 
				'group_id' =>  $group_id
			)
		);
	}

	public function getFields( array $meta_boxes ) {

	}

    /**
     * Testet, ob  ein Benutzer überhaupt in einer Gruppe ist.
     *
     * @param $id
     * @return mixed
     */
	public function getUserNotInGroup($id) {
		global $wpdb;

	    $table_useringroup = $this->dbTableUserInGroup;
	    $table_wpuser = $wpdb->prefix . "users";

	    $results = $wpdb->get_results("SELECT $table_wpuser.user_nicename, $table_wpuser.ID FROM wp_users LEFT OUTER JOIN $table_useringroup ON wp_users.ID = $table_useringroup.user_id AND $table_useringroup.group_id=$id WHERE $table_useringroup.group_id IS NULL ");

	    return( $results);
	}

    /**
     * Holt eine Gruppe von der Datenbank.
     * @param $id DB-Id der Gruppe
     * @return mixed Gruppeninformationen
     */
	public function getGroup($id) {
		global $wpdb;

	    $table_name = $this->dbTableGroup;

	    $results = $wpdb->get_row( "SELECT * FROM  $table_name WHERE id=$id");

	    return( $results);
	}

    /**
     * Gibt die Benutzer einer Gruppe zurück.
     * @param $id
     * @return mixed
     */
	public function getGroupAndUsers($id) {
		global $wpdb;
		$table_useringroup = $this->dbTableUserInGroup;
		$table_wpuser = $wpdb->prefix . "users";

		$group = $this->getGroup($id);
		$group->user = $wpdb->get_results("SELECT user_id, $table_wpuser.user_nicename FROM  $table_useringroup right outer join $table_wpuser on $table_useringroup.user_id=$table_wpuser.ID WHERE $table_useringroup.group_id=$id");
		return $group;
	}

    /**
     * Speichert/Erstellt eine Gruppe
     *
     * @param $name
     * @param $description
     * @param $user_id
     */
	public function saveGroup($name, $description, $user_id) {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$table_name = $this->dbTableGroup;

		$sql = $wpdb->insert(
			$table_name, 
			array( 
				'name' => $name, 
				'description' => $description
			)
		);

		// Person, welche die Gruppe erstellt hat, wird zur Gruppe hinzugefügt
		$table_connect = $this->dbTableUserInGroup;
		$sql = $wpdb->insert(
			$table_connect, 
			array( 
				'user_id' => $user_id, 
				'group_id' =>  $wpdb->insert_id 
			)
		);
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
		$tableGroups = $this->dbTableGroup;

	    $sql = "CREATE TABLE IF NOT EXISTS $tableGroups (
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
		$tableUserInGroups = $this->dbTableUserInGroup ;
		$wps_usertable = $wpdb->prefix . "users";

	    $sql = "CREATE TABLE IF NOT EXISTS $tableUserInGroups (
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
	}
}

?>