<?php 
class ThemeVotesDB {
	function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->ThemeVotes_dbversion = '1.1';
		$this->table_name = 'ThemeVotes';
	}
	public function create() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $this->table_name;

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			uid longtext NOT NULL,
			rate longtext NOT NULL,
			date longtext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( 'ThemeVotes_version', $this->ThemeVotes_dbversion );
	}
	public function check() {
	    if ( get_site_option( 'ThemeVotes_version' ) != $this->ThemeVotes_dbversion ) {
	        $this->create();
	    }
	}
	public function add( $info=array() ) {
	 	global $wpdb;
	 	$table_name = $this->table_name;
	 	if( $this->exists($info['uid']) ) {
	 		$this->delete( array("uid"=>$info['uid']) );
	 	}
	 	
 		$info['rate'] = maybe_serialize($info['rate']);
 		$info['date'] = time();
		$result = $wpdb->insert(
			$this->table_name,
			$info
		);
		if( !$result ) {
			return false;
		}
		return $wpdb->insert_id;
	}
	public function query( $args=array(), $AND='AND' ) {
		if( !isset($args['per']) ) {
		}else {
			$per = $args['per'];
			unset($args['per']);
		}
		if( !isset($args['offset']) ) {
			$offset = 0;
		}else {
			$offset = $args['offset'];
			unset($args['offset']);
		}
	 	global $wpdb;
	 	$table_name = $this->table_name;
	 	$Operator = "SELECT * FROM `$table_name`";
	 	$WHERE = ' WHERE';
	 	$AND = ' '.$AND;
	 	$i = 0;
	 	foreach ($args as $k => $v) {
	 		if( $v == "*" ) {
	 		}else {
	 			if( is_numeric($v) ) {
		 			$v = "= $v";
		 		}else {
		 			$v = "= '$v'";
		 		}
		 		if( $i == 0 ) {
			 		$Operator .= "$WHERE $k $v";
			 	}else {
			 		$Operator .= "$AND $k $v";
			 	}
			 	$i++;
			 }
	 	}
	 	$Operator .= " ORDER BY CAST(date AS SIGNED) DESC";
	 	if( isset($per) ) {
	 		$Operator .= " LIMIT $offset,$per";
	 	}
	 	$result = $wpdb->get_results($Operator);
		return $result;
	}
	public function count( $args=array(), $AND='AND' ) {
		if( !isset($args['per']) ) {
		}else {
			$per = $args['per'];
			unset($args['per']);
		}
		if( !isset($args['offset']) ) {
			$offset = 0;
		}else {
			$offset = $args['offset'];
			unset($args['offset']);
		}
	 	global $wpdb;
	 	$table_name = $this->table_name;
	 	$Operator = "SELECT COUNT(*) as count FROM `$table_name`";
	 	$WHERE = ' WHERE';
	 	$AND = ' '.$AND;
	 	$i = 0;
	 	foreach ($args as $k => $v) {
	 		if( $v == "*" ) {
	 		}else {
	 			if( is_numeric($v) ) {
		 			$v = "= $v";
		 		}else {
		 			$v = "= '$v'";
		 		}
		 		if( $i == 0 ) {
			 		$Operator .= "$WHERE $k $v";
			 	}else {
			 		$Operator .= "$AND $k $v";
			 	}
			 	$i++;
			 }
	 	}
	 	$Operator .= " ORDER BY CAST(date AS SIGNED) DESC";
	 	if( isset($per) ) {
	 		$Operator .= " LIMIT $offset,$per";
	 	}
	 	$result = $wpdb->get_row($Operator);
		return $result->count;
	}
	public function exists( $uid ) {
	    global $wpdb;
	    $table_name = $this->table_name;
	 	$Operator = "SELECT * FROM `$table_name` WHERE uid = '$uid'";
	 	$result = $wpdb->get_row($Operator);
	 	if( !$result ) return false;
	 	$result = (ARRAY) $result;
	 	$result['rate'] = maybe_unserialize($result['rate']);
	 	return $result;
	}
	public function delete( $where=array() ) {
	 	global $wpdb;
	 	$table_name = $this->table_name;

 		$wpdb->delete($table_name, $where);
	}
}
add_action("after_switch_theme", array((new ThemeVotesDB), "create"));
add_action( 'plugins_loaded', array((new ThemeVotesDB), "check") );