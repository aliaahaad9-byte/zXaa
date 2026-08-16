<?phpglobal $ThemeStatic;
$VideosDB = new VideosDB;
if(isset(($_GET['post']))){
	$postID = $_GET['post'];
	$post = get_post($postID);
}else{
	global $post;
	$postID = $post->ID;
}
//
$VideosArgums = $VideosDB->get(
	array(
		'PostID'=>$post->ID,
	),
	0,100
);
//
echo '<FieldsIdsAttrs class="ModeAfilit">';
	echo '<div class="ErrorAlertRelative"></div>';
	echo '<AddMoreVista>';
		echo '<div class="HTwo--TitleGroup">إنشاء عنصر جديد <i class="fa-solid fa-angle-left"></i><span>إضافة فيديو جديد الى محتويات المحاضرة </span></div>';
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">عنوان الفيديو </h2>';
			echo '<input type="text" placeholder="العنوان" name="InsertTitle" id="CustomInsertTitle" data-important="true">';
		echo '</SingleElement--Field>';
		CustomUploadsField(
			array(
				'id'=>'Insertwatch',
				'name'=>'رفع فيديو المشاهدة',
				'multiple'=>false,
				'mime'=>'video/mp4',
			)
		);
		CustomUploadsField(
			array(
				'id'=>'Insertlisten',
				'name'=>'رفع ملف الاستماع',
				'multiple'=>false,
				'mime'=>'audio/mpeg',
			)
		);
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">مجانى ؟</h2>';
			echo '<SwitchField data-field="Insertfree">';
				echo '<input type="checkbox" name="Insertfree" id="Insertfree" style="display:none">';
				echo '<div class="Switch">';
					echo '<span>معطّل</span>';
					echo '<strong>مفعّل</strong>';
					echo '<em></em>';
				echo '</div>';
			echo '</SwitchField>';
		echo '</SingleElement--Field>';
		echo '<InsertMoreVista data-groupmore="VideosDB" data-template="VideosDB" data-post="'.$post->ID.'"><i class="far fa-plus"></i><span>أنشاء عنصر جديد </span></InsertMoreVista>';
	echo '</AddMoreVista>';
	echo '<ul class="ItemsGroupsUI">';
		if(!empty($VideosArgums)){
			$VideosCount = $VideosDB->count(array('PostID'=>$post->ID));
			echo '<div class="Title--SectionsAll">';
				echo '<i class="fa-solid fa-clapperboard"></i>';
				echo '<p>';
					echo '<span>الفيديوهات قَمت بإضافتها إلى المحاضرة </span>';
					echo '<strong>'.$VideosCount.'</strong><span>فيديو</span>';
				echo '</p>';

			echo '</div>';
			foreach ($VideosArgums as $lope => $video) {
				$ThemeStatic->Part('FildesModels',array('DbObject'=>$video,'post'=>$post,'file'=>'VideosDB'));
			}
		}
	echo '</ul>';
echo '</FieldsIdsAttrs>';