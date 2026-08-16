<?php class YourColor__URL_DB {
	function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->YourColor__URL_DB_dbversion = '1.1';
		$this->table_name = 'YourColor__URL_DB';
		$this->DefultFields = array('callcount','calldate');
		$this->MyFields = array('callcount','calldate','date','data_options');
	}
	public function create() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $this->table_name;

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			callcount longtext NOT NULL,
			calldate longtext NOT NULL,
			date longtext NOT NULL,
			data_options longtext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( 'YourColor__URL_DB_version__v2', $this->YourColor__URL_DB_dbversion );
	}
	public function check() {
	    if ( get_site_option( 'YourColor__URL_DB_version__v2' ) != $this->YourColor__URL_DB_dbversion ) {
	        $this->create();
	    }
	}

	public function count($data, $offset=0, $per='-1' ) {
	 	global $wpdb;
	 	$table_name = $this->table_name;

	 	if( isset( $data['paged'] ) ) $offset = ( $data['paged']-1 ) * $per;
	 	unset( $data['paged'] );

	 	if(isset($data['selected']) and !empty($data['selected'])){
	 		$soleted = $data['selected'];
	 		$sqlOperator = "SELECT COUNT(`$soleted`) as count FROM $table_name";
	 	}else{
	 		$sqlOperator = "SELECT COUNT(*) as count FROM $table_name";
	 	}
	 	if( !isset( $data['operator'] ) ){
		 	$CiloPtra = false;
		 	foreach ($data as $skey => $meky) {
		 		if(in_array($skey,$this->MyFields) || $skey == 'id'){
		 			$CiloPtra = true;
		 			$sar[] = $skey.' = "'.$meky.'"';
		 		}
		 	}
	 		if($CiloPtra == true){
	 			$sqlOperator .= ' WHERE ( ';
			 	foreach ($sar as $seky => $meky) {
			 		if($seky > 0){
			 			$sqlOperator .= ' AND ';
			 		}
			 		$sqlOperator .= $meky;
			 	}
			 	$sqlOperator .= ' )';
		 	}
	 	}
	 	if( isset( $data['operator'] ) ) $sqlOperator .= $data['operator'];
	 	#
	 	if(isset($data['orderby'])){
	 		if(!isset($data['order'])) $data['order'] = 'DESC';
	 		$sqlOperator .= ' ORDER BY CAST('.$data['orderby'].' AS SIGNED INT) '.$data['order'];
	 	}
	 	if( $per > 0 ) {
	 		$sqlOperator .= " LIMIT $per";
	 	}
	 	if( $offset > 0 ) {
	 		$sqlOperator .= " OFFSET $offset";
	 	}

	 	$row = $wpdb->get_row($sqlOperator);
	 	
	 	if(isset($row->count)) return $row->count;
	 	if(isset($row[0]->count)) return $row[0]->count;
		return false;
	}	
	public function get($search__object, $offset=0, $per='-1' ) {
	 	global $wpdb;
	 	$table_name = $this->table_name;


	 	if( isset( $search__object['paged'] ) ) $offset = ( $search__object['paged']-1 ) * $per;
	 	unset( $search__object['paged'] );

	 	# SELECT .
	 		$SELECT = ( ( isset( $search__object['SELECT'] ) ) ) ? $search__object['SELECT'] : '*';
	 	# COUNT .	
	 		$COUNT = ( ( isset( $search__object['COUNT(*)'] ) ) ) ? ", {$search__object['COUNT(*)']}" : "";

	 	# GROUP CONCAT .	
	 		$GROUP_CONCAT = ( ( isset( $search__object['GROUP_CONCAT'] ) ) ) ? ", GROUP_CONCAT({$search__object['GROUP_CONCAT']})" : "";

	 	# GROUP BY .	
	 		$GROUP_BY = ( ( isset( $search__object['GROUP_BY'] ) ) ) ? " GROUP BY {$search__object['GROUP_BY']}" : "";

	 	# AS .
	 		$AS = ( ( isset( $search__object['AS'] ) ) ) ? "as {$search__object['AS']}" : "";

	 	# WHERE FIELDS .
	 		$WHERE = '';
	 		$fields = $this->MyFields;
	 		if( !in_array( 'id' , $fields) ) $fields[] = 'id';

	 		# EXTRACT WHERE FIELDS .
	 			$where__fields__list = array();
		 		foreach ( $search__object as $field => $value ) {
		 			if( in_array( $field , $fields ) ){

		 				$Compare__Value = ( ( isset( $search__object['compare'][$field] ) ) ) ? $search__object['compare'][$field] : '=';
		 				$where__fields__list[] = "{$field} {$Compare__Value} '{$value}'";
		 			}
		 		}
		 	# WHERE APPEND LIST 	
		 		if( !empty( $where__fields__list ) ){
		 			$WHERE .= " WHERE ( ";
		 				$i=0;
		 				foreach( $where__fields__list as $field__text ){$i++;
		 					$WHERE .= ( ( $i > 1 ) ) ? ' AND ' : '';
		 					$WHERE .= $field__text;
		 				}
		 			$WHERE .= " )";
		 		}

	 	$sql_operator = "SELECT {$SELECT}{$COUNT}{$GROUP_CONCAT}{$AS} FROM {$table_name}{$WHERE}{$GROUP_BY}";

		# ORDER && ORDERBY 
		 	if(isset($search__object['orderby'])){
		 		if(!isset($search__object['order'])) $search__object['order'] = 'DESC';
		 		$sql_operator .= ' ORDER BY CAST('.$search__object['orderby'].' AS SIGNED) '.$search__object['order'];
		 	}

		# PER  	
		 	if( $per > 0 ) {
		 		$sql_operator .= " LIMIT $per";
		 	}

		# OFFSET 	
		 	if( $offset > 0 ) {
		 		$sql_operator .= " OFFSET $offset";
		 	}		

		# TEST . 	
	 		//echo $sql_operator;die();

	 	# GET ROWS .	
	 		$row = $wpdb->get_results($sql_operator);
		 	if( !$row ) {
		 		return false;
		 	}else {
		 		$return = maybe_unserialize($row);
		 	}

		# EXTRACT DATA BY ( GROUP_BY ) && RETURN THIS GROUP .
			if( isset( $search__object['GroupExtract'] ) && isset( $search__object['GROUP_BY'] ) ){
				$GroupExtract = array();
				foreach ( $return as $k => $single__row ) {
					
					$search__object['AS'] = ( ( isset( $search__object['AS'] ) ) ) ? 'list' : $search__object['AS'];
				    $single__row->{$search__object['AS']} = explode(',', $single__row->{$search__object['AS']});
				    $single__row->{$search__object['AS']} = ( ( is_array( $single__row->{$search__object['AS']} ) ) ) ? $single__row->{$search__object['AS']} : array( $single__row->{$search__object['AS']} );

				    $GroupExtract[ $single__row->{$search__object['GROUP_BY']} ][ $search__object['AS'] ] = $single__row->{$search__object['AS']};

				    if( isset( $search__object['COUNT(*)'] ) ){
				    	$GroupExtract[ $single__row->{$search__object['GROUP_BY']} ]['count'] = $single__row->{$search__object['COUNT(*)']};
				    }

				}
				return $GroupExtract;
			}

		return $return;
	}

	public function add($data=array()) {
	 	global $wpdb;
	 	//
	 	$InsertrComps = array();
	 	if(empty($data)) return false;
	 	foreach ($this->MyFields as $seky => $meky) {
	 		if(isset($data[$meky])){
		 		if(is_array($data[$meky])){
		 			$InsertrComps[$meky] = maybe_serialize($data[$meky]);
		 		}else{
		 			$InsertrComps[$meky] = $data[$meky];
		 		}
	 		}else{
	 			$InsertrComps[$meky] = '';
	 		}
	 	}
	 	$stotime = time();
		$InsertrComps['date'] = $stotime;
		//
		if(!isset($InsertrComps['data_options'])){
			$options = array();
	 		$options = maybe_serialize($options);
	 		$InsertrComps['data_options'] = $options;
		}
		$result = $wpdb->insert(
			$this->table_name,
			$InsertrComps
		);
		if( !$result ) {
			return false;
		}
		return $wpdb->insert_id;
	}
	public function UpdateRowMeta($data,$id){
	 	global $wpdb;
	 	//
	 	$InsertrComps = array();
	 	if(empty($data)) return false;
	 	foreach ($this->MyFields as $seky => $meky) {
	 		if(isset($data[$meky])){
		 		if(is_array($data[$meky])){
		 			$InsertrComps[$meky] = maybe_serialize($data[$meky]);
		 		}else{
		 			$InsertrComps[$meky] = $data[$meky];
		 		}
	 		}
	 	}
		$result = $wpdb->update($this->table_name, $InsertrComps, array('id'=>$id));
		if( !$result ) {
			return false;
		}
		return $wpdb->insert_id;
	}
	public function update($data=array()){
	 	global $wpdb;
	 	$table_name = $this->table_name;
	 	
	 	if(!isset($data['id'])){
		 	if(!isset($data['check'])) $data['check'] = $this->DefultFields;
	 		$CheckData = array();
	 		foreach ($data['check'] as $skey => $meky) {
	 			if(!isset($data[$meky])) return false;
	 			$CheckData[$meky] = $data[$meky];
	 		}
	 		unset($data['check']);
			$HowToGet = $this->get($CheckData);
	 		if($HowToGet == false){
			  $result = $this->add($data);
			  $return = $result;
			}else{
		 		if( isset( $data['toggle'] ) && $data['toggle'] != false ){ # Remove or Add
					$return = $this->RemoveID($HowToGet[0]->id);
		 		}else{
					$return = $this->UpdateRowMeta($data,$HowToGet[0]->id);
		 		}
			}
	 	}else{
	 		$return = $this->UpdateRowMeta($data,$data['id']);
	 	}
		return $return;
	}
	public function RemoveID($id ) {
	 	global $wpdb, $current_user;
	 	$table_name = $this->table_name;
	 	//
 		$deleteOperator = $wpdb->delete($table_name,array('id'=>$id));
		if( $deleteOperator ) {
    	return array('type'=>'remove','alert'=>'success');
    }else{
    	return array('type'=>'remove','alert'=>'error');
    }
	}
}
add_action("after_switch_theme", array((new YourColor__URL_DB), "create"));
add_action( 'plugins_loaded', array((new YourColor__URL_DB), "check") );