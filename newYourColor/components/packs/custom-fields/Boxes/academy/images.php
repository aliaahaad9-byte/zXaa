<?php  $Termo = get_term_by('id',$_GET['tag_ID'],'courses');
$Certificate = (is_array(get_term_meta($Termo->term_id,'Certificate',true))) ? get_term_meta($Termo->term_id,'Certificate',true) : array();
$Fonst = array(
	'Readex_Pro'=>array(
		'name'=>'Readex Pro',
		'url'=>'https://fonts.googleapis.com/css2?family=Readex+Pro:wght@200;300;400;500;600;700&display=swap',
	),
	'Kufam'=>array(
		'name'=>'Kufam',
		'url'=>'https://fonts.googleapis.com/css2?family=Kufam:ital,wght@0,400;0,500;0,600;0,700;0,900;1,400;1,500;1,600;1,700&display=swap',
	),
	'Changa'=>array(
		'name'=>'Changa',
		'url'=>'https://fonts.googleapis.com/css2?family=Changa:wght@200;300;400;500;600;700;800&display=swap',
	),
	'El_Messiri'=>array(
		'name'=>'El Messiri',
		'url'=>'https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&display=swap',
	),
	'Noto_Kufi+Arabic'=>array(
		'name'=>'Noto Kufi Arabic',
		'url'=>'https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100;200;300;400;500;600;700;800;900&display=swap',
	),
	'IBM_Plex_Sans_Arabic'=>array(
		'name'=>'IBM Plex Sans Arabic',
		'url'=>'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap',
	),
	'Tajawal'=>array(
		'name'=>'Tajawal',
		'url'=>'https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap',
	),
);
//
if(isset($Certificate['font'])){
	echo "<style id='MyStyleChanged'>@import url('".$Fonst[$Certificate['font']]['url']."')</style>";
}
echo '<div class="InsertCertificateBord">';
	echo '<div class="CertificateBordHeader">';
		echo '<div class="MyStorySet--Certificate">';
			echo SotrySetIcon('CertificateAdmin');
		echo '</div>';
		echo '<h2>تحديد صورة الشهادة النهائية .. التى سيحصل عليها الطالب فور الانتهاء من الدورة </h2>';
		echo '<SBM--BTNArea>';
			if(isset($Certificate['url'])){
				echo '<div class="Certificated--BTN BTN--ShowOptions"><i class="fa-solid fa-arrows-maximize"></i>إعادة  تعيين بيانات الشهادة</div>';
				echo '<div class="CertificateUploaded Certificated--BTN"><i class="fas fa-upload"></i>إعادة تعيين الشهادة </div>';	
			}else{
				echo '<div class="CertificateUploaded Certificated--BTN"><i class="fas fa-upload"></i>رفع صورة الشهادة </div>';	
			}
		echo '</SBM--BTNArea>';
	echo '</div>';
	echo '<div class="Certifivated--Area">';
		echo '<div class="Certificate--UploaderAppender">';
			if(isset($Certificate['url'])){
				echo '<div class="Current--Certificate">';
					echo '<img src="'.$Certificate['url'].'">';
					echo '<name--certificate style="color:'.$Certificate['color'].';font-size:'.$Certificate['range'].'px ;background:'.$Certificate['background'].';font-family:'.$Certificate['font'].';width:'.$Certificate['width'].'px;height:'.$Certificate['height'].'px;'.$Certificate['position'].'" class="draggable ui-widget-content">'.$Certificate['name'].'</name--certificate>';
				echo '</div>';
				echo '<input type="hidden" class="inputurl" value="'.$Certificate['url'].'" name="Certificate[url]">';
				echo '<input type="hidden" class="inputname" value="'.$Certificate['name'].'" name="Certificate[name]">';
				echo '<input type="hidden" class="inputwidth" value="'.$Certificate['width'].'" name="Certificate[width]">';
				echo '<input type="hidden" class="inputheight" value="'.$Certificate['height'].'" name="Certificate[height]">';
				echo '<input type="hidden" class="inputfont" value="'.$Certificate['font'].'" name="Certificate[font]">';
				echo '<input type="hidden" class="inputcolor" value="'.$Certificate['color'].'" name="Certificate[color]">';
				echo '<input type="hidden" class="inputbackground" value="'.$Certificate['background'].'" name="Certificate[background]">';
				echo '<input type="hidden" class="inputrange" value="'.$Certificate['range'].'" name="Certificate[range]">';
				echo '<input type="hidden" class="inputposition" value="'.$Certificate['position'].'" name="Certificate[position]">';
			}
		echo '</div>';

		echo'<div class="coboard--settings--form '.((isset($Certificate['url'])) ? 'showsin' : '').'" style="top:100px;left:100px;">';
			echo '<My--H2Title><i class="fa-solid fa-money-check-pen"></i>إعدادات الشهادة الاساسية </My--H2Title>';
			echo'<div class="Congratulation--form--input">';
				echo'<label>إسم الشخص </label>';
				echo'<input type="text" name="Imagename" data-changer="Certificate[name]" value="'.((isset($Certificate['name'])) ? $Certificate['name'] : 'اسمك الكريم  ').'" placeholder="اسمك  ">';
			echo'</div>';

			echo'<div class="Congratulation--form--input">';
				echo'<label>عرض مربع الاسم </label>';
				echo'<input type="text" name="width" data-changer="Certificate[width]" value="'.((isset($Certificate['width'])) ? $Certificate['width'] : 'اسمك الكريم  ').'" placeholder="اسمك  ">';
			echo'</div>';

			echo'<div class="Congratulation--form--input">';
				echo'<label>طول </label>';
				echo'<input type="text" name="height" data-changer="Certificate[height]" value="'.((isset($Certificate['height'])) ? $Certificate['height'] : 'اسمك الكريم  ').'" placeholder="اسمك  ">';
			echo'</div>';

			echo'<div class="Congratulation--form--input">';
				echo'<label>نوعه الخط </label>';
				//
				echo '<select name="font" id="font" data-changer="Certificate[font]">';
				foreach ($Fonst as $v => $option) {
					$FindSelected = (( isset($Certificate['font']) )) ? $Certificate['font'] : 'Changa';
					echo '<option'.(($FindSelected == $v) ? ' selected' : '').' value="'.$v.'">'.$option['name'].'</option>';
				}
				echo '</select>';
			echo'</div>';

			echo'<div class="Congratulation--form--input colorrang">';
				echo'<label>لون المربع </label>';
				echo'<input type="color" name="background" data-changer="Certificate[background]" value="'.((isset($Certificate['background'])) ? $Certificate['background'] : 'ffffff').'">';
			echo'</div>';

			echo'<div class="Congratulation--form--input colorrang">';
				echo'<label>اللون  </label>';
				echo'<input type="color" name="color" data-changer="Certificate[color]" value="'.((isset($Certificate['color'])) ? $Certificate['color'] : '000000').'">';
			echo'</div>';
			echo'<div class="Congratulation--form--input colorrang">';
				echo'<label>حجم الخط  </label>';
				echo'<input type="range" min="0" max="100" value="'.((isset($Certificate['range'])) ? $Certificate['range'] : '20').'" step="1" name="range" data-changer="Certificate[range]">';
			echo'</div>';
			echo'<exportButton>';
				echo'<span class="ClosseElements">إلغاء</span>';
				echo'<span class="SaveImageOptions">حفظ & متابعة </span>';
			echo'</exportButton>';
		echo'</div>';

	echo '</div>';
echo '</div>';
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		var FontsObject = <?=json_encode($Fonst);?>;
		$('.CertificateUploaded').click(function(e){e.preventDefault();
			var image = wp.media({ 
		   	title: 'رفع صورة الشهادة النهائية  ',
		   	multiple: false
			}).open()
			.on('select', function(e){
		   	var uploaded_image = image.state().get('selection');
				uploaded_image.each(function(attachment) {
					console.log(attachment);
					var image_url = attachment.toJSON().url;
					// # MY HTML ..
					HTML_Uploading = '<div class="Current--Certificate">';
						HTML_Uploading += '<img src="'+image_url+'">';
						HTML_Uploading += '<name--certificate style="width:300px;color:#000000;font-size:20px ;left:50%;top:50%;background:#ffffff;font-family:Changa;height:50px" class="draggable ui-widget-content">إسم الشخص </name--certificate>';
					HTML_Uploading += '</div>';
					HTML_Uploading += '<removImg></removImg>';
					HTML_Uploading += '<input type="hidden" class="inputurl" value="'+image_url+'" name="Certificate[url]">';
					HTML_Uploading += '<input type="hidden" class="inputname" value="إسم الشخص " name="Certificate[name]">';
					HTML_Uploading += '<input type="hidden" class="inputwidth" value="300" name="Certificate[width]">';
					HTML_Uploading += '<input type="hidden" class="inputheight" value="50" name="Certificate[height]">';
					HTML_Uploading += '<input type="hidden" class="inputfont" value="Changa" name="Certificate[font]">';
					HTML_Uploading += '<input type="hidden" class="inputcolor" value="#000000" name="Certificate[color]">';
					HTML_Uploading += '<input type="hidden" class="inputbackground" value="#ffffff" name="Certificate[background]">';
					HTML_Uploading += '<input type="hidden" class="inputrange" value="20" name="Certificate[range]">';
					HTML_Uploading += '<input type="hidden" class="inputposition" value=""name="Certificate[position]">';
					$('.Certificate--UploaderAppender').html(HTML_Uploading);
					//
					RePostions();
					$('.Certifivated--Area').addClass('showsin');
					//
			 	});
			});
		});
		// # SHOW OPTIONS 
		var LastImageHTML;
		$('body').on("click",'.BTN--ShowOptions',function(){
			var MyColor,MyRange,MyPostion,MyName,MyImage,MyWidth,MyFont,MyBG,MyHeight;
  		MyColor = $('.Certificate--UploaderAppender input[name="Certificate[color]"]').val();
  		MyBG = $('.Certificate--UploaderAppender input[name="Certificate[background]"]').val();
  		MyRange = $('.Certificate--UploaderAppender input[name="Certificate[range]"]').val();
			MyPostion = $('.Certificate--UploaderAppender input[name="Certificate[position]"]').val();
			MyName = $('.Certificate--UploaderAppender input[name="Certificate[name]"]').val();
			MyImage = $('.Certificate--UploaderAppender input[name="Certificate[url]"]').val();
			MyWidth = $('.Certificate--UploaderAppender input[name="Certificate[width]"]').val();
			MyHeight = $('.Certificate--UploaderAppender input[name="Certificate[height]"]').val();
			MyFont = $('.Certificate--UploaderAppender input[name="Certificate[font]"]').val();

			LastImageHTML = '<div class="Current--Certificate">';
				LastImageHTML += '<img src="'+MyImage+'">';
				LastImageHTML += '<name--certificate style="color:'+MyColor+';font-size:'+MyRange+'px ;'+MyPostion+';background:'+MyBG+';font-family:'+MyFont+';width:'+MyWidth+'px;height:'+MyHeight+'px" class="draggable ui-widget-content">'+MyName+'</name--certificate>';
			LastImageHTML += '</div>';
			LastImageHTML += '<removImg></removImg>';
			LastImageHTML += '<input type="hidden" class="inputurl" value="'+MyImage+'" name="Certificate[url]">';
			LastImageHTML += '<input type="hidden" class="inputname" value="'+MyName+'"  name="Certificate[name]">';
			LastImageHTML += '<input type="hidden" class="inputwidth" value="'+MyWidth+'" name="Certificate[width]">';
			LastImageHTML += '<input type="hidden" class="inputheight" value="'+MyHeight+'" name="Certificate[height]">';
			LastImageHTML += '<input type="hidden" class="inputfont" value="'+MyFont+'" name="Certificate[font]">';
			LastImageHTML += '<input type="hidden" class="inputcolor" value="'+MyColor+'" name="Certificate[color]">';
			LastImageHTML += '<input type="hidden" class="inputbackground" value="'+MyBG+'" name="Certificate[background]">';
			LastImageHTML += '<input type="hidden" class="inputrange" value="'+MyRange+'" name="Certificate[range]">';
			LastImageHTML += '<input type="hidden" class="inputposition" value="'+MyPostion+'" name="Certificate[position]">';

			$('.Certifivated--Area').addClass('showsin');
			$('.Certifivated--Area').addClass('showClosse');
		});	
		// CLOSSE CHANGES IMAGE ..
    $('body').on('click','.ClosseElements',function(){
    	$('.Certifivated--Area').removeClass('showsin showClosse');

    	if(LastImageHTML != ''){
    		$('.Certificate--UploaderAppender').html(LastImageHTML);
    		RePostions(true);
    	}else{
    		$('.Certificate--UploaderAppender').html('');
    	}
    });
    // # SAVED CHANHGE ..
    $('body').on('click','.SaveImageOptions',function(){
    	$('.Certifivated--Area').removeClass('showsin showClosse');
    });

		// REMOVE IMAGE ..
    $('body').on('click','removimg',function(){
    	$(this).parent().remove();
    });
    // 
    $('body').on('click','.BTN--ShowOptions',function(){
    	$('.Certifivated--Area').addClass('showsin');
    });
    // ## REPOSTION ..
    function RePostions(ts=false) {
    	if( $('.Current--Certificate').length > 0 || ts == true){
    		var MyColor,MyRange,MyPostion,MyName,MyImage,MyWidth,MyFont,MyBG,MyHeight;
    		MyColor = $('.Certificate--UploaderAppender input[name="Certificate[color]"]').val();
    		MyBG = $('.Certificate--UploaderAppender input[name="Certificate[background]"]').val();
    		MyRange = $('.Certificate--UploaderAppender input[name="Certificate[range]"]').val();
  			MyPostion = $('.Certificate--UploaderAppender input[name="Certificate[position]"]').val();
  			MyName = $('.Certificate--UploaderAppender input[name="Certificate[name]"]').val();
  			MyImage = $('.Certificate--UploaderAppender input[name="Certificate[url]"]').val();
  			MyWidth = $('.Certificate--UploaderAppender input[name="Certificate[width]"]').val();
  			MyHeight = $('.Certificate--UploaderAppender input[name="Certificate[height]"]').val();
  			MyFont = $('.Certificate--UploaderAppender input[name="Certificate[font]"]').val();
  			// # ..
		    $(".draggable").draggable({
		     	drag: function(event,ui){
				    var dragposition = ui.position;
				    $('name--certificate').attr('data-left',dragposition.left);
				    $('name--certificate').attr('data-top',dragposition.top);
				    $('name--certificate').attr('pos','left: '+dragposition.left+'px;top:'+dragposition.top+'px;');
    				$('input.inputposition').val('left: '+dragposition.left+'px;top:'+dragposition.top+'px;');
				  }
			 	});
			 	$('.Congratulation--form--input input[name="name"]').val(MyName);
			 	$('.Congratulation--form--input input[name="color"]').val(MyColor);
			 	$('.Congratulation--form--input input[name="range"]').val(MyRange);
			 	$('.Congratulation--form--input input[name="width"]').val(MyWidth);
			 	$('.Congratulation--form--input input[name="height"]').val(MyHeight);
			 	$('.Congratulation--form--input input[name="font"]').val(MyFont);
			 	$('.Congratulation--form--input input[name="background"]').val(MyBG);
    	}
    	if($('.coboard--settings--form').data('doneload') == undefined){
    		$('.coboard--settings--form').attr("doneload",true);
		    $(".coboard--settings--form").draggable({
		     	drag: function(event,ui){
				    var dragposition = ui.position;
				    $(".coboard--settings--form").attr('data-left',dragposition.left);
				    $(".coboard--settings--form").attr('data-top',dragposition.top);
				    $(".coboard--settings--form").attr('pos','left: '+dragposition.left+'px;top:'+dragposition.top+'px;');
				  }
			 	});
    	}
    }
    RePostions();
    // ## ..
    $('body').on('keyup input change','.Congratulation--form--input input, .Congratulation--form--input select',function(){
    	var InputElem,InputName;
    	InputElem = $(this);
    	InputName = InputElem.data('changer');
    	//
    	if(InputElem.attr('name') == 'Imagename'){
				$('name--certificate').html(InputElem.val());	
    	}else if(InputElem.attr('name') == 'width'){
				$('name--certificate').css("width",InputElem.val());	
    	}else if(InputElem.attr('name') == 'height'){
				$('name--certificate').css("height",InputElem.val());				
    	}else if(InputElem.attr('name') == 'font'){
				if($('#MyStyleChanged').length > 0){
					$('#MyStyleChanged').html('@import url('+FontsObject[InputElem.val()].url+')');
				}else{
					$('body').append("<style id='MyStyleChanged'>@import url('"+FontsObject[InputElem.val()].url+"')</style>");
				}
				$('name--certificate').css("font-family",FontsObject[InputElem.val()].name);
    	}else if(InputElem.attr('name') == 'background'){
				$('name--certificate').css("background",InputElem.val());				
    	}else if(InputElem.attr('name') == 'color'){
				$('name--certificate').css("color",InputElem.val());	
    	}else if(InputElem.attr('name') == 'range'){
				$('name--certificate').css("font-size",InputElem.val()+'px');	
    	}
			$('.Certificate--UploaderAppender input[name="'+InputName+'"]').val(InputElem.val());
    });	
	});
</script>