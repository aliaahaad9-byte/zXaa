<?phpglobal $ThemeStatic;
$QuestionExams = new QuestionExams;
if(isset(($_GET['post']))){
	$postID = $_GET['post'];
	$post = get_post($postID);
}else{
	global $post;
	$postID = $post->ID;
}
//
$QustionsArgums = $QuestionExams->get(
	array(
		'PostID'=>$post->ID,
	),
	0,100
);
//
echo '<FieldsIdsAttrs class="ModeAfilit">';
	echo '<div class="ErrorAlertRelative"></div>';
	echo '<AddMoreVista>';
		echo '<div class="HTwo--TitleGroup">إنشاء سؤال  جديد <i class="fa-solid fa-angle-left"></i><span>إضافة اسئلة لأختبارات المحاضرة </span></div>';

		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">السؤال </h2>';
			echo '<input type="text" placeholder="مثال : ماهي البرمجة  ?" name="Question" id="CustomQuestion" data-important="true">';
		echo '</SingleElement--Field>';
		echo '<SingleElement--Field class="FieldIsSelectFields">';
			echo '<h2 class="TitleGrupsFields">تحديد نوع إدخال الأجابة</h2>';
			$ThemeStatic->Part(
				'SelectFieldType',
				array(
					'id'=>'QustionType',
				)
			);
		echo '</SingleElement--Field>';
		//
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">إجابة السؤال </h2>';
			echo '<input type="text" placeholder="مثال : 1" name="Answer" id="CustomAnswer" data-important="true">';
		echo '</SingleElement--Field>';
		// ## ExamScore
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">الدرجة النهائية </h2>';
			echo '<input type="number" placeholder="مثال : 10" name="ExamScore" id="CustomExamScore" data-important="true">';
		echo '</SingleElement--Field>';
		// ## ExamTime
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">مدة الاجابة على السؤال  ( عدد الثوانى  )</h2>';
			echo '<input type="number" placeholder="مثال : 10" name="ExamTime" id="CustomExamScore">';
		echo '</SingleElement--Field>';
		// ## Noticeable
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">ملاحظات </h2>';
			echo '<textarea style="height:80px" placeholder="إضف ملاحظات عامة بخصوص السؤال " name="Noticeable" id="Noticeable"></textarea>';
		echo '</SingleElement--Field>';

		// ## IMPORTANT
		echo '<SingleElement--Field>';
			echo '<h2 class="TitleGrupsFields">سؤال إجباري</h2>';
			echo '<SwitchField data-field="Important" class="active">';
				echo '<input type="checkbox" checked="checked" name="Important" id="Important" style="display:none">';
				echo '<div class="Switch">';
					echo '<span>معطّل</span>';
					echo '<strong>مفعّل</strong>';
					echo '<em></em>';
				echo '</div>';
			echo '</SwitchField>';
		echo '</SingleElement--Field>';		

		echo '<InsertMoreVista data-groupmore="QuestionExams" data-template="QuestionExams" data-post="'.$post->ID.'"><i class="far fa-plus"></i><span>أنشاء عنصر جديد </span></InsertMoreVista>';
	echo '</AddMoreVista>';
	echo '<ul class="ItemsGroupsUI">';
		if(!empty($QustionsArgums)){
			$QuestionsCount = $QuestionExams->count(array('PostID'=>$post->ID));
			echo '<div class="Title--SectionsAll">';
				echo '<i class="fa-solid fa-clapperboard"></i>';
				echo '<p>';
					echo '<span>الأسئلة التي قَمت بإضافتها إلى أختبارا المحاضرة </span>';
					echo '<strong>'.$QuestionsCount.'</strong><span>سؤال</span>';
				echo '</p>';
			echo '</div>';
			foreach ($QustionsArgums as $lope => $qustion) {
				$ThemeStatic->Part('FildesModels',array('DbObject'=>$qustion,'post'=>$post,'file'=>'QuestionExams'));
			}
		}
	echo '</ul>';
echo '</FieldsIdsAttrs>';