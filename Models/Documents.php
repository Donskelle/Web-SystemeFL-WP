<?php
class Documents {



	public function initDocumentsDatabase() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/**
		 * Datenbank für Dokumente
		 */
		//$table_name = $wpdb->prefix . 'DokuMummy_Documents';

	    //$sql = "CREATE TABLE $table_name (
	    //  id int(11) NOT NULL AUTO_INCREMENT,
	    //  name varchar(255) DEFAULT NULL,
	    //  description varchar(255) DEFAULT NULL,
	    //  active int(1) DEFAULT 1,
	    //  UNIQUE KEY id (id)
	    //);";
	
	}

}

?>