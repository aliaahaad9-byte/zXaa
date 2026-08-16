<?php 
if(isset(($_GET['post']))){
	$postID = $_GET['post'];
	$faq = (is_array(get_post_meta($postID,'faq',true))) ? get_post_meta($postID,'faq',true) : array();
}else if(isset($_GET['tag_ID'])){
	$postID = $_GET['tag_ID'];
	$faq = (is_array(get_term_meta($postID,'faq',true))) ? get_term_meta($postID,'faq',true) : array();
}else{
	global $post;
	$postID = $post->ID;
	$faq = (is_array(get_post_meta($postID,'faq',true))) ? get_post_meta($postID,'faq',true) : array();
}
echo '<grp-FieldsIdsAttrs class="ModeAfilit">';
	echo '<div class="ErrorAlertRelative"></div>';
	echo '<grp-AddMoreVista>';
		echo '<input type="text" placeholder="السؤال" autocomplete="off" autocorrect="off" name="question" spellcheck="false" attrtype="text">';
		echo '<textarea name="answer" attrtype="textarea" spellcheck="false"></textarea>';
		echo '<grp-InsertMoreVista data-grp-groupmore="faq" data-template="faq"><i class="far fa-plus"></i><span>أنشاء عنصر جديد </span></grp-InsertMoreVista>';
	echo '</grp-AddMoreVista>';
	echo '<ul class="ItemsGroupsUl apbsortable">';
		if(!empty($faq)){
			foreach ($faq as $lope => $mone) {
				echo '<li attkey="'.$lope.'" attrfield="faq">';
					echo '<input type="text" placeholder="السؤال"  autocomplete="off" autocorrect="off" name="faq['.$lope.'][question]" value="'.$mone['question'].'">';
					echo '<textarea type="text" placeholder="الاجابة " autocomplete="off" autocorrect="off" name="faq['.$lope.'][answer]">'.$mone['answer'].'</textarea>';
					echo '<span onclick="$(this).parent().remove();"><i class="fa fa-times"></i></span>';
				echo '</li>';
			}
		}
	echo '</ul>';
echo '</grp-FieldsIdsAttrs>';