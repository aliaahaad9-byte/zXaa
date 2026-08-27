<?php
class ReactTrigger {
	function __construct($tname='like') {
		if( $tname == 'like' ) {
			$tname = 'Likes';
		}else if( $tname == 'sad' ) {
			$tname = 'Sad';
		}else if( $tname == 'wow' ) {
			$tname = 'Wow';
		}else if( $tname == 'angry' ) {
			$tname = 'Angry';
		}else if( $tname == 'haha' ) {
			$tname = 'Haha';
		}else if( $tname == 'love' ) {
			$tname = 'Love';
		}
		$this->table_name = $tname;
	}
	public function NToTable($tname) {
		if( $tname == 'like' ) {
			$tname = 'Likes';
		}else if( $tname == 'sad' ) {
			$tname = 'Sad';
		}else if( $tname == 'wow' ) {
			$tname = 'Wow';
		}else if( $tname == 'angry' ) {
			$tname = 'Angry';
		}else if( $tname == 'haha' ) {
			$tname = 'Haha';
		}else if( $tname == 'love' ) {
			$tname = 'Love';
		}
		return $tname;
	}
	public function count( $postID, $type='post', $total=false ) {
	 	global $wpdb;
	 	$table_name = $this->table_name;
	 	//
	 	$Operator = "SELECT COUNT(userID) AS count FROM `Likes` WHERE `postID` = '$postID' AND `type` = '$type'";
	 	$likes = $wpdb->get_row($Operator);
	 	$likes_count = $likes->count;

	 	$Operator = "SELECT COUNT(userID) AS count FROM `Sad` WHERE `postID` = '$postID' AND `type` = '$type'";
	 	$sad = $wpdb->get_row($Operator);
	 	$sad_count = $sad->count;

	 	$Operator = "SELECT COUNT(userID) AS count FROM `Wow` WHERE `postID` = '$postID' AND `type` = '$type'";
	 	$wow = $wpdb->get_row($Operator);
	 	$wow_count = $wow->count;

	 	$Operator = "SELECT COUNT(userID) AS count FROM `Angry` WHERE `postID` = '$postID' AND `type` = '$type'";
	 	$angry = $wpdb->get_row($Operator);
	 	$angry_count = $angry->count;

	 	$Operator = "SELECT COUNT(userID) AS count FROM `Haha` WHERE `postID` = '$postID' AND `type` = '$type'";
	 	$haha = $wpdb->get_row($Operator);
	 	$haha_count = $haha->count;

	 	$Operator = "SELECT COUNT(userID) AS count FROM `Love` WHERE `postID` = '$postID' AND `type` = '$type'";
	 	$love = $wpdb->get_row($Operator);
	 	$love_count = $love->count;
	 	//
	 	if( $total == false ) {
			$emotes = array(
				'like'	=> $likes_count,
				'sad'	=> $sad_count,
				'wow'	=> $wow_count,
				'angry'	=> $angry_count,
				'haha'	=> $haha_count,
				'love'	=> $love_count,
			);
		}else {
			$emotes = $likes_count + $sad_count + $wow_count + $angry_count + $haha_count + $love_count;
		}
		return $emotes;
	}
	public function get( $postID, $type='post', $react='all', $sorted=0, $offset=0 ) {
	 	global $wpdb;
	 	//
	 	if( $react == 'all' ) {
		 	$Operator = "SELECT *, 'Likes' AS 'table' FROM `Likes` WHERE `postID` = '$postID' AND `type` = '$type'
		 	UNION
		 	SELECT *, 'Sad' AS 'table' FROM `Sad` WHERE `postID` = '$postID' AND `type` = '$type'
		 	UNION
		 	SELECT *, 'Wow' AS 'table' FROM `Wow` WHERE `postID` = '$postID' AND `type` = '$type'
		 	UNION
		 	SELECT *, 'Angry' AS 'table' FROM `Angry` WHERE `postID` = '$postID' AND `type` = '$type'
		 	UNION
		 	SELECT *, 'Haha' AS 'table' FROM `Haha` WHERE `postID` = '$postID' AND `type` = '$type'
		 	UNION
		 	SELECT *, 'Love' AS 'table' FROM `Love` WHERE `postID` = '$postID' AND `type` = '$type'
		 	ORDER BY CAST(date AS int) DESC LIMIT $offset,19";
		 	$reacts = $wpdb->get_results($Operator);
		 	//
		 	$emotes = $reacts;
	 	}else {
		 	$table_name = $this->table_name;
		 	$Operator = "SELECT *, '$table_name' AS 'table' FROM `$table_name` WHERE `postID` = '$postID' AND `type` = '$type' ORDER BY CAST(date AS int) DESC LIMIT $offset,19";
		 	$reacts = $wpdb->get_results($Operator);
		 	//
		 	$emotes = $reacts;
		}
		return $emotes;
	}
	public function activities( $userID, $offset=0, $per=20 ) {
	 	global $wpdb;

	 	//
	 	$Operator = "SELECT *, 'Likes' AS 'table' FROM `Likes` WHERE `userID` = '$userID'
	 	UNION
	 	SELECT *, 'Sad' AS 'table' FROM `Sad` WHERE `userID` = '$userID'
	 	UNION
	 	SELECT *, 'Wow' AS 'table' FROM `Wow` WHERE `userID` = '$userID'
	 	UNION
	 	SELECT *, 'Angry' AS 'table' FROM `Angry` WHERE `userID` = '$userID'
	 	UNION
	 	SELECT *, 'Haha' AS 'table' FROM `Haha` WHERE `userID` = '$userID'
	 	UNION
	 	SELECT *, 'Love' AS 'table' FROM `Love` WHERE `userID` = '$userID'
	 	ORDER BY CAST(date AS int) DESC LIMIT $offset,$per";
	 	$reacts = $wpdb->get_results($Operator);
	 	//
		return $reacts;
	}
	public function latest_activities( $offset=0, $per=20 ) {
	 	global $wpdb;
	 	//
	 	$Operator = "SELECT *, 'Likes' AS 'table' FROM `Likes` WHERE `type` = 'post' or `type` = 'series'
	 	UNION
	 	SELECT *, 'Sad' AS 'table' FROM `Sad` WHERE `type` = 'post' or `type` = 'series'
	 	UNION
	 	SELECT *, 'Wow' AS 'table' FROM `Wow` WHERE `type` = 'post' or `type` = 'series'
	 	UNION
	 	SELECT *, 'Angry' AS 'table' FROM `Angry` WHERE `type` = 'post' or `type` = 'series'
	 	UNION
	 	SELECT *, 'Haha' AS 'table' FROM `Haha` WHERE `type` = 'post' or `type` = 'series'
	 	UNION
	 	SELECT *, 'Love' AS 'table' FROM `Love` WHERE `type` = 'post' or `type` = 'series'
	 	ORDER BY CAST(date AS int) DESC LIMIT $offset,$per";
	 	$reacts = $wpdb->get_results($Operator);
	 	//
		return $reacts;
	}
	public function name($react) {
		$name = '';
		if( $react == 'like' ) {
			$name = 'أعجبني';
		}else if( $react == 'sad' ) {
			$name = 'أحزنني';
		}else if( $react == 'wow' ) {
			$name = 'واااو';
		}else if( $react == 'angry' ) {
			$name = 'أغضبني';
		}else if( $react == 'haha' ) {
			$name = 'هاهاها';
		}else if( $react == 'love' ) {
			$name = 'أحببته';
		}
		return $name;
	}
	public function exists_all( $postID, $userID, $type, $detail=0 ) {
		$return = false;
		if( $this->exists($postID, $userID, $type, 'Likes') ) {
			$return = ($detail == 0) ? true : 'like';
		}else if( $this->exists($postID, $userID, $type, 'Sad') ) {
			$return = ($detail == 0) ? true : 'sad';
		}else if( $this->exists($postID, $userID, $type, 'Wow') ) {
			$return = ($detail == 0) ? true : 'wow';
		}else if( $this->exists($postID, $userID, $type, 'Angry') ) {
			$return = ($detail == 0) ? true : 'angry';
		}else if( $this->exists($postID, $userID, $type, 'Haha') ) {
			$return = ($detail == 0) ? true : 'haha';
		}else if( $this->exists($postID, $userID, $type, 'Love') ) {
			$return = ($detail == 0) ? true : 'love';
		}
		return $return;
	}
	public function exists( $postID, $userID, $type, $table_name=false ) {
	 	global $wpdb;
	 	if( $table_name == false ) {
		 	$table_name = $this->table_name;
		}

	 	$query = $wpdb->get_var("SELECT * FROM $table_name WHERE userID = '$userID' AND postID = '$postID' AND type = '$type' LIMIT 1");
	 	$return = false;
	 	if( !empty($query) ) {
	 		$return = true;
	 	}
	 	return $return;
	}
	public function add( $info=array() ) {
	 	global $wpdb;
	 	$info['date'] = time();
	 	$table_name = $this->table_name;
		$result = $wpdb->insert(
			$this->table_name,
			$info
		);
		if( !$result ) {
			return false;
		}
		return true;
	}
	public function delete( $postID, $userID, $type ) {
	 	global $wpdb;
	 	$table_name = $this->table_name;

 		$wpdb->delete($table_name, array('postID'=>$postID, 'userID'=>$userID, 'type'=>$type));
	}
}
