<?php
function CommonKeywords($title, $content) {
	$words_leader = array();
	$triplewords = array();
	$i = 0;
	$mastkey = 0;
	$words_bug = explode(' ', trim(strip_tags($title.' '.$content)));
	foreach( $words_bug as $k => $word ) {
		$word = trim($word);
		$word = str_replace(array('"', ':', ' ', '"', ',', "،"), '', $word);
		$words_bug[$k] = $word;
		if( mb_strlen($word) < 5 ) {
			unset($words_bug[$k]);
		}
	}
	foreach( $words_bug as $k => $word ) {
		if( mb_strlen($word) >= 5 ) {
			$i++;
			$words_leader[] = $word;
		}
		$words_leader[] = ((isset($words_bug[$k - 1])) ? $words_bug[$k - 1].' ' : '').$word;
		$words_leader[] = ((isset($words_bug[$k - 1])) ? $words_bug[$k - 1].' ' : '').$word.((isset($words_bug[$k + 1])) ? ' '.$words_bug[$k + 1] : '');
	}
	$words_leader = array_unique($words_leader);
	$stop_words = explode(',', get_option('stop_words'));
	$mostly_searching_sorting = array();
	foreach( $words_leader as $word ) {
		if( !in_array($word, $stop_words) ) {
			$found = mb_substr_count(trim(strip_tags($content)), $word);
			if( $found >= 1 ) {
				$mostly_searching_sorting[$word] = $found;
			}
		}
	}
	arsort($mostly_searching_sorting);
	return array_keys($mostly_searching_sorting);
}