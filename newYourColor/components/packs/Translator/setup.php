<?php
function TranslateStrings($string, $target, $from='auto') {
    $string = urlencode($string);

    $result = (new ThemeStatic)->file_get_contents('https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl='.$from.'&tl='.$target.'&q='.$string);
    $result = json_decode($result, true);
    if( empty($result) or !is_array($result) ) {
        $result = (new ThemeStatic)->file_get_contents('https://clients5.google.com/translate_a/t?client=dict-chrome-ex&sl='.$from.'&tl='.$target.'&dt=t&q='.trim($string));
        $result = json_decode($result, true);

        return ((isset($result[0])) ? $result[0] : $result);
    }
    if( isset($result[0]) ) {
        $text = '';
        foreach( $result[0] as $sentence ) {
            if( isset($sentence[1]) and isset($sentence[0]) ) {
                if( strpos($string, $sentence[0]) !== false ) {
                    $text .= htmlspecialchars_decode($sentence[1]);
                }else {
                    $text .= htmlspecialchars_decode($sentence[0]);
                }
            }
        }
        return str_replace( array(' ,', '، '), array(',', '،'), $text );
    }
}