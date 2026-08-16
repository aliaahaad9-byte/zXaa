<?php 
function WidgetInput($args=array()) {
	if( isset($args['name']) ) {
		$title = isset($args['title']) ? $args['title'] : '';
		$style = isset($args['style']) ? $args['style'] : '';
		$name = isset($args['name']) ? $args['name'] : '';
		$singleID = isset($args['singleID']) ? $args['singleID'] : '';
		$id = isset($args['id']) ? $args['id'] : '';
		$instance = isset($args['instance']) ? $args['instance'] : '';
		$repeat = isset($args['repeat']) ? $args['repeat'] : '';
		$currentValue = ($repeat == true) ? array() : '';


		
		if( isset($instance[$singleID]) and $repeat == true ) {
			$currentValue = $instance[$singleID];
		}
		echo '<theme-widget data-repeat="'.$repeat.'">';
			#
			$reachedGroup = false;
			if( $repeat == true and !empty($currentValue) ) {
				$i = 0;
				foreach( $currentValue as $k => $row ) {
					$i++;
					ob_start();
					echo '<theme-widget-options>';

						echo '<h2'.( isset($args['closed']) && ($args['closed'] != true) ? ' class="open"' : '').'>';
							echo '<i class="fa fa-caret-left"></i>';
							echo '<p>';
								echo $title;
								if( $repeat == true ) {
									echo ' [<strong class="counter" data-key="'.$k.'" data-i="'.$i.'">'.$i.'</strong>]';
								}
							echo '</p>';
							echo '<theme-widget-action class="remove hoverable activable"><i class="fa fa-trash"></i></theme-widget-action>';
						echo '</h2>';
						
						echo '<theme-widget-stack'.( isset($args['closed']) && ($args['closed'] != true) ? '  style="display:none;"' : '').'>';
							foreach( $args['stack'] as $field ) {
								if( $repeat == true ) {
									$field['name'] = $args['name'].'['.$k.']['.$field['singleID'].']';
									$field['id'] = $args['id'].'_'.$k.'_'.$field['id'];
								}
								$currentValue = $row[$field['singleID']];
								$field['currentValue'] = $currentValue;
								WidgetInput__Element($args, $field);
							}
						echo '</theme-widget-stack>';
					echo '</theme-widget-options>';
					$reachedGroup = true;
				}
			}
			ob_start();
			echo '<theme-widget-options>';
				echo '<h2'.(( isset( $args['closed'] ) && $args['closed'] != true) ? ' class="open"' : '').'>';
					echo '<i class="fa fa-caret-left"></i>';
					echo '<p>';
						echo $title;
						if( $repeat == true ) {
							echo ' [<strong class="counter" data-key="{key}" data-i="{num}">{num}</strong>]';
						}
					echo '</p>';
					if( $repeat == true ) {
						echo '<theme-widget-action class="remove '.(($reachedGroup == true) ? 'hoverable activable' : '').'"><i class="fa fa-trash"></i></theme-widget-action>';
					}
				echo '</h2>';
				echo '<theme-widget-stack'.(( isset( $args['closed'] ) && $args['closed'] == true) ? ' style="display:none;"' : '').'>';
					foreach( $args['stack'] as $field ) {
						if( $repeat == true ) {
							$field['name'] = $args['name'].'[{key}]['.$field['singleID'].']';
							$field['id'] = $args['id'].'_{key}_'.$field['id'];
						}
						$currentValue = '';
						if( isset($instance[$field['singleID']]) and $repeat == false ) {
							$currentValue = $instance[$field['singleID']];
						}
						$field['currentValue'] = $currentValue;
						WidgetInput__Element($args, $field);

					}
				echo '</theme-widget-stack>';
			echo '</theme-widget-options>';
			$clean = ob_get_clean();
			if( $reachedGroup == false ) {
				echo str_replace(array("{num}", "{key}"), array("1", "0"), $clean);
			}
			if( $repeat == true ) {
				echo '<theme-widget-actions>';
					echo '<div style="display:none;" structure>'.base64_encode($clean).'</div>';
					echo '<theme-widget-action class="addmore hoverable activable"><i class="fa fa-plus"></i> إضافة المزيد</theme-widget-action>';
				echo '</theme-widget-actions>';
			}
		echo '</theme-widget>';
	}
}
function WidgetInput__Element($parent, $field) {
	echo '<label for="'.$field['id'].'">';
		echo '<span>'.$field['title'].'</span>';
		switch ($field['type']) {
			case 'oembed':
	
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}

				$oembed_link = '';
				$cls = '';
				if ($field['currentValue'] != '') {

					preg_match('/src="([^"]+)"/', $field['currentValue'], $match);
					$url = $match[1];

					if (strpos($url, 'youtube') !== false) {
						$cls = 'youtube';
						$id_code = explode('embed/', $url)[1];
						$oembed_link = 'https://www.youtube.com/watch?v='.$id_code;

					}else if(strpos($url, 'instagram') !== false){
						$cls = 'instagram';
						$id_code = explode('p/', $url)[1];
						$id_code = explode('/', $id_code)[0];
						$oembed_link = 'https://www.instagram.com/p/'.$id_code.'';
					}else if(strpos($url, 'twitter') !== false){
						$cls = 'twitter';
						$id_code = explode('twitter.com/Interior/status/', $field['currentValue'])[1];
						$id_code = explode('?ref', $id_code)[0];
						$oembed_link = 'https://twitter.com/Interior/status/'.$id_code;
					}

				}

		
				echo '<textarea style="display:none"  name="'.$field['name'].'" id="'.$field['id'].'">'.$field['currentValue'].'</textarea>';

				echo '<input type="text" value="'.$oembed_link.'" name="'.$name.'_yc" class="add-youtube-link" '.$field_att.'  />';
				echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
				if ($field['currentValue'] != '') {
					echo '<youtube--code class="'.$cls.'">';
						echo '<remove--youtube class="fa-solid fa-xmark"></remove--youtube>';
						echo $field['currentValue'];
					echo '</youtube--code>';
				}
			break;
			case 'time':
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}

				echo '<input type="text" '.$field_att.' class="show-time"  name="'.$field['name'].'" id="'.$field['id'].'" value="'.$field['currentValue'].'"/>';
				echo '<time--box>';
					echo '<time--box--title>'.(is_rtl() ? 'اختار الوقت' : 'Choose Time' ).'</time--box--title>';
					echo '<div class="dflex">';
						echo '<div class="choose-hour">';
							echo '<input type="number"  placeholder="'.(is_rtl() ? ' الساعة' : 'Hour' ).'" />';
							echo '<hours>';
							for ($i=1; $i <= 12 ; $i++) { 
								echo '<hour>'.($i < 10 ? '0'.$i : $i).'</hour>';
							}
							echo '</hours>';
						echo '</div>';
						echo '<div class="choose-min">';
							echo '<input type="number"  placeholder="'.(is_rtl() ? 'الدقيقة' : 'Minute' ).'" />';
							echo '<minutes>';
								for ($i=0; $i <= 59 ; $i++) { 
									echo '<minute>'.($i < 10 ? '0'.$i : $i).'</minute>';
								}
							echo '</minutes>';
						echo '</div>';
						echo '<div class="choose-am-pm">';
							echo '<input type="text"  placeholder="'.(is_rtl() ? 'ص-م' : 'am-pm' ).'" />';
							echo '<ampm>';
								echo '<ampm-options>am</ampm-options>';
								echo '<ampm-options>pm</ampm-options>';
							echo '</ampm>';
						echo '</div>';
					echo '</div>';
					echo '<div class="choose-date">'.(is_rtl() ? 'حفظ' : 'Save' ).'</div>';
				echo '</time--box>';
			break;
			case 'text':
				$field_att = '';
				if (isset( $field['attributes'] )) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}
				if (isset($field['repeatable'])) {
					echo '<div class="all-values">';

						echo '<div class="all-fields">';
							if (empty($field['currentValue'])) {
								echo '<input type="text" class="repeatable first-element" value="'.$field['currentValue'].'" name="'.$field['name'].'[]" '.$field_att.' id="'.$field['id'].'_yc_0" />';
							}else {
								foreach ($field['currentValue'] as $i =>  $v) {
									echo $i > 0 ? '<div class="el-field">' : '';
										echo '<input class="repeatable '.($i == 0 ? 'first-element' : '' ).'"  type="text" value="'.$v.'" name="'.$field['name'].'[]" '.$field_att.' id="'.$field['id'].'_yc_'.$i.'" />';
									echo $i > 0 ? '<i class="fa fa-times"></i></div>' : '';
								}
							}
						echo '</div>';

						echo '<div class="add-field"><i class="fa fa-plus"></i></div>';

					echo '</div>';
				}else{
					echo '<input type="text" value="'.$field['currentValue'].'" name="'.$field['name'].'" '.$field_att.' id="'.$field['id'].'" />';
				}
			break;
			case 'number':
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}
				echo '<input type="number" '.$field_att.' name="'.$field['name'].'" id="'.$field['id'].'" value="'.$field['currentValue'].'" />';
			break;
			case 'email':
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}
				echo '<input type="email" '.$field_att.' name="'.$field['name'].'" id="'.$field['id'].'" value="'.$field['currentValue'].'" />';
			break;
			case 'checkbox':
				if (isset($field['tabs'])) {
			
					echo '<checkbox--tabs>';
						foreach ($field['tabs'] as $i=> $k ) {
							echo '<check--tab '.($i == 0 ? 'class="active"' : '' ).' data-id="#'.str_replace(' ','',$k).'">';
								echo $k;
							echo '</check--tab>';
						}
					echo '</checkbox--tabs>';

					echo '<checkbox--options-container>';
						echo '<checkbox--options>';
							$k=0;
							$j = 0;
							foreach ($field['options'] as $key => $arr) {
								echo '<div  '.($j == 0 ? 'class="open"' : '' ).' id="'.str_replace(' ','',$key).'">';
								if (isset($field['images']) && $field['images'] == true ) {
									foreach ($arr as $v => $opt) {
									$k++;
										$idCheckbox = $field['id'].$k;
										echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';
											echo '<input type="checkbox" id="'.$idCheckbox.'" name="'.$field['name'].'[]"  value="'.$v.'"
											'.((in_array($v, (is_array($field['currentValue'])) ? $field['currentValue'] : array())) ? ' checked' : '').'
											 />';
											echo '<div class="toggle"></div>';
											echo '<div class="flex-1"><div class="preview-image"><img src="'.$opt.'" /></div><img src="'.$opt.'" /></div>';
										echo '</label>';
									}
								}else{
									foreach ($arr as $v => $opt) {
										$k++;
										$idCheckbox = $field['id'].$k;
										echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
											echo '<input type="checkbox" id="'.$idCheckbox.'" name="'.$field['name'].'[]"  value="'.$v.'"
											'.((in_array($v, (is_array($field['currentValue'])) ? $field['currentValue'] : array())) ? ' checked' : '').'
											 />';
											 
											 echo '<div class="toggle"></div>';
											echo '<em>'.$opt.'</em>';
										echo '</label>';
									}
								}
								echo '</div>';
								$j++;
							}
						echo '</checkbox--options>';
					echo '</checkbox--options-container>';

				}else{

					if( isset($field['options']) ) {

						if (isset($field['images']) && $field['images'] == true) {
							$k=0;
							foreach ($field['options'] as $v => $opt) {$k++;
								$idCheckbox = $field['id'].$k;
								echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';
									echo '<input type="checkbox" id="'.$idCheckbox.'" name="'.$field['name'].'[]"  value="'.$v.'"
											'.((in_array($v, (is_array($field['currentValue'])) ? $field['currentValue'] : array())) ? ' checked' : '').'
											 />';
									echo '<div class="toggle"></div>';
									echo '<div class="flex-1-preview-">
										<div class="preview-image">
											<img src="'.$opt.'" />
										</div>
										<i class="fas fa-window"></i>
									</div>';

								echo '</label>';
							}

						}else {

							$k=0;
							foreach ($field['options'] as $v => $opt) {$k++;
								$idCheckbox = $field['id'].$k;
								echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';

									echo '<input type="checkbox" id="'.$idCheckbox.'" name="'.$field['name'].'[]"  value="'.$v.'"
											'.((in_array($v, (is_array($field['currentValue'])) ? $field['currentValue'] : array())) ? ' checked' : '').'
											 />';

									echo '<div class="toggle"></div>';
									echo '<em>'.$opt.'</em>';
								echo '</label>';
							}
						}

					}else {
						
						$value = $field['currentValue'];
						echo '<label class="APBCheckLabel" for="'.$field['id'].'">';
							echo '<input type="checkbox" name="'.$field['name'].'" id="'.$field['id'].'"  '.(($field['currentValue'] == 'on') ? 'checked' : '').' />';
							echo '<div class="toggle"></div>';
						echo '</label>';
					}

				}
			break;
			case 'icons':
				if (isset( $field['library'] )) {	
					$library = $field['library'];
					echo '<font--icons>';
						echo '<input type="hidden" data-url="'.admin_url('admin-ajax.php' ).'"  value="'.$field['currentValue'].'" name="'.$field['name'].'"  />';
						echo '<input type="text" class="choose-fonts"  value="'.str_replace(['fa-','fa ','ion-ios-'],'',$field['currentValue']).'" data-name="'.$field['name'].'"  id="'.$field['id'].'" />';
							echo '<fonts--box>';
								echo '<div class="remove-font-icons fa fa-times"></div>';
								echo '<div class="font-title">';
									echo 'اختر الايقونة';
								echo '</div>';
								echo '<div class="search-icons">';
									echo '<input type="text" placeholder="'.( $library == 'lordicons' ? 'اكتب الid مثل tyounuzx' : 'اكتب اسم الايقونة' ).'"   data-url="'.admin_url('admin-ajax.php' ).'" data-font="'.$library.'" />';
								echo '</div>';
								if ($library == 'fontawesome') {
									echo '<all-icons>';
										echo '<div class="fa fa-abacus"></div>';
										echo '<div class="fa fa-accent-grave"></div>';
										echo '<div class="fa fa-acorn"></div>';
										echo '<div class="fa fa-address-book"></div>';
										echo '<div class="fa fa-contact-book"></div>';
										echo '<div class="fa fa-address-card"></div>';
										echo '<div class="fa fa-contact-card"></div>';
										echo '<div class="fa fa-vcard"></div>';
										echo '<div class="fa fa-air-conditioner"></div>';
										echo '<div class="fa fa-airplay"></div>';
										echo '<div class="fa fa-alarm-clock"></div>';
										echo '<div class="fa fa-alarm-exclamation"></div>';
										echo '<div class="fa fa-alarm-plus"></div>';
										echo '<div class="fa fa-alarm-snooze"></div>';
										echo '<div class="fa fa-album"></div>';
										echo '<div class="fa fa-album-circle-plus"></div>';
										echo '<div class="fa fa-album-circle-user"></div>';
										echo '<div class="fa fa-album-collection"></div>';
										echo '<div class="fa fa-album-collection-circle-plus"></div>';
										echo '<div class="fa fa-album-collection-circle-user"></div>';
										echo '<div class="fa fa-alicorn"></div>';
										echo '<div class="fa fa-alien-monster"></div>';
										echo '<div class="fa fa-align-center"></div>';
										echo '<div class="fa fa-align-justify"></div>';
										echo '<div class="fa fa-align-left"></div>';
										echo '<div class="fa fa-align-right"></div>';
										echo '<div class="fa fa-align-slash"></div>';
										echo '<div class="fa fa-alt"></div>';
										echo '<div class="fa fa-amp-guitar"></div>';
										echo '<div class="fa fa-ampersand"></div>';
									echo '</all-icons>';
								}else if($library == 'ionicons'){
									echo '<all-icons>';
									echo ' <div class="ion-alert"></div>';
									echo ' <div class="ion-alert-circled"></div>';
									echo ' <div class="ion-android-add"></div>';
									echo ' <div class="ion-android-add-circle"></div>';
									echo ' <div class="ion-android-alarm-clock"></div>';
									echo ' <div class="ion-android-alert"></div>';
									echo ' <div class="ion-android-apps"></div>';
									echo ' <div class="ion-android-archive"></div>';
									echo ' <div class="ion-android-arrow-back"></div>';
									echo ' <div class="ion-android-arrow-down"></div>';
									echo ' <div class="ion-android-arrow-dropdown"></div>';
									echo ' <div class="ion-android-arrow-dropdown-circle"></div>';
									echo ' <div class="ion-android-arrow-dropleft"></div>';
									echo ' <div class="ion-android-arrow-dropleft-circle"></div>';
									echo ' <div class="ion-android-arrow-dropright"></div>';
									echo ' <div class="ion-android-arrow-dropright-circle"></div>';
									echo ' <div class="ion-android-arrow-dropup"></div>';
									echo ' <div class="ion-android-arrow-dropup-circle"></div>';
									echo ' <div class="ion-android-arrow-forward"></div>';
									echo ' <div class="ion-android-arrow-up"></div>';
									echo ' <div class="ion-android-attach"></div>';
									echo ' <div class="ion-android-bar"></div>';
									echo ' <div class="ion-android-bicycle"></div>';
									echo '<div class="ion-android-boat"></div>';
									echo '<div class="ion-android-bookmark"></div>';
									echo '<div class="ion-android-bulb"></div>';
									echo '<div class="ion-android-bus"></div>';
									echo '<div class="ion-android-calendar"></div>';
									echo '<div class="ion-android-camera"></div>';
									echo '<div class="ion-android-car"></div>';
									echo '</all-icons>';	
								}else {
									echo '<all-icons>';	
										echo '<lord-icon src="https://cdn.lordicon.com/lupuorrc.json" data-id="lupuorrc" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/hrqwmuhr.json" data-id="hrqwmuhr" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/wxnxiano.json" data-id="wxnxiano" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/yalwfksd.json" data-id="yalwfksd" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/jvucoldz.json" data-id="jvucoldz" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/iltqorsz.json" data-id="iltqorsz" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/slkvcfos.json" data-id="slkvcfos" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/gqdnbnwt.json" data-id="gqdnbnwt" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/dxjqoygy.json" data-id="dxjqoygy" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/nkmsrxys.json" data-id="nkmsrxys" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/qhgmphtg.json" data-id="qhgmphtg" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/nocovwne.json" data-id="nocovwne" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/vixtkkbk.json" data-id="vixtkkbk" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/gmzxduhd.json" data-id="gmzxduhd" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/tyounuzx.json" data-id="tyounuzx" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/krmfspeu.json" data-id="krmfspeu" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/dzydjxom.json" data-id="dzydjxom" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/iiueiwdd.json" data-id="iiueiwdd" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/wnkegycl.json" data-id="wnkegycl" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/rvuqcvqy.json" data-id="rvuqcvqy" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/qvzrpodt.json" data-id="qvzrpodt" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/xgeyogar.json" data-id="xgeyogar" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/koyivthb.json" data-id="koyivthb" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/pithnlch.json" data-id="pithnlch" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/dzllstvg.json" data-id="dzllstvg" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/eszyyflr.json" data-id="eszyyflr" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/bwnhdkha.json" data-id="bwnhdkha" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/raayvuis.json" data-id="raayvuis" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/nobciafz.json" data-id="nobciafz" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/xnfkhcfn.json" data-id="xnfkhcfn" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/smeqxwcv.json" data-id="smeqxwcv" trigger="loop"></lord-icon>';
										echo '<lord-icon src="https://cdn.lordicon.com/bgwzirmj.json" data-id="bgwzirmj" trigger="loop"></lord-icon>';
									echo '</all-icons>';	
								}

							echo '</fonts--box>';

						if ($field['currentValue'] != '') {
							echo '<icons>';
							if ($library !== 'lordicons') {
								echo '<div class="'.$field['currentValue'].'"></div>';
							}else if( $library == 'lordicons') {
								echo '<lord-icon src="https://cdn.lordicon.com/'.$field['currentValue'].'.json"  trigger="loop" style="width:80px;height:80px"></lord-icon>';
							}
							echo '</icons>';
						}
					echo '</font--icons>';
				}
			break;
			case 'radio' :
				if (isset($field['images']) && $field['images'] == true) {
					$k=0;
					foreach ($field['options'] as $v => $opt) {$k++;
						$idCheckbox = $field['name'].$k;
						echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';
							echo '<input'.(($v == $field['currentValue']) ? ' checked' : '').' type="radio" value="'.$v.'" name="'.$field['name'].'" id="'.$idCheckbox.'" />';
							echo '<div class="toggle"></div>';
							echo '<div class="flex-1-preview-">
								<div class="preview-image">
									<img src="'.$opt.'" />
								</div>
								<i class="fas fa-window"></i>
							</div>';
						echo '</label>';
					}
				}else{

					$k=0;
					foreach ($field['options'] as $v => $opt) {$k++;
						$idCheckbox = $field['name'].$k;
						echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
							echo '<input'.(($v == $field['currentValue']) ? ' checked' : '').' type="radio" value="'.$v.'" name="'.$field['name'].'" id="'.$idCheckbox.'" />';
							echo '<div class="toggle"></div>';
							echo '<em>'.$opt.'</em>';
						echo '</label>';
					}
				}
			break;
			case 'textarea':
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}
				echo '<textarea '.$field_att.' name="'.$field['name'].'" id="'.$field['id'].'">'.$field['currentValue'].'</textarea>';
			break;
			case 'colorpicker':
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}
				echo '<input type="text" '.$field_att.' value="'.$field['currentValue'].'" class="ColorViewer" name="'.$field['name'].'" id="'.$field['id'].'" />';
			break;
			case 'date':
				$field_att = '';
				if (isset($field['attributes'])) {
					foreach ($field['attributes'] as $key => $v) {
						$field_att.=' '.$key.'="'.$v.'" ';
					}
				}
				echo '<input type="date" '.$field_att.' value="'.$field['currentValue'].'" name="'.$field['name'].'" id="'.$field['id'].'" />';
			break;
			case 'file':
				echo '<file-list-upload>';
					echo '<input type="hidden" value="'.((isset($field['id'])) ? $field['id'] : '').'" name="'.$field['name'].'[id]" id="'.$field['id'].'_id" />';
					echo '<input type="text" value="'.((isset($field['currentValue']['url'])) ? $field['currentValue']['url'] : '').'" name="'.$field['name'].'[url]" id="'.$field['id'].'" />';
					echo '<a href="javascript:void(0);" data-multiple="false" data-type="image" data-field="#'.$field['id'].'" data-name="'.$field['name'].'" data-rlname="'.$field['name'].'" class="YTSUploadButton hoverable">رفع صور</a>';
					if( empty($field['currentValue']) ) {$style='display:none;';}
					echo '<img class="YTSPreviewFile" id="'.$field['id'].'_preview"  src="'.((isset($field['currentValue']['url'])) ? $field['currentValue']['url'] : '').'" />';
					echo '<a  href="javascript:void(0);" class="YTSRemoveButton" data-multiple="false" id="'.$field['id'].'_remove">'.((is_rtl()) ? 'حذف الكل' : 'Remove all').'</a>';
				echo '</file-list-upload>';
			break;
			case 'file_list':
				echo '<file-list-upload>';
					echo '<a href="javascript:void(0);" data-multiple="true" data-type="image" data-field="#'.$field['id'].'" data-name="'.$field['name'].'" data-rlname="'.$field['name'].'" class="YTSUploadButton hoverable">رفع صور</a>';
					echo '<div class="previewList" id="'.$field['id'].'_preview">';
						foreach ((is_array($field['currentValue'])) ? $field['currentValue'] : array() as $k => $url) {
							echo '<span><input type="hidden" name="'.$field['name'].'['.$k.']" value="'.$url.'" /><em onClick="this.parent().remove();"><span></span><span></span></em><img src="'.$url.'" /></span>';
						}
					echo '</div>';
					if( empty($field['currentValue']) ) {$style='display:none;';}
					echo '<a  href="javascript:void(0);" class="YTSRemoveButton" data-multiple="true" id="'.$field['id'].'_remove">'.((is_rtl()) ? 'حذف الكل' : 'Remove all').'</a>';
				echo '</file-list-upload>';
			break;
			case 'select' : 
				echo '<select name="'.$field['name'].'" id="'.$field['id'].'">';
					echo '<option value="">إختر</option>';
					foreach( $field['options'] as $value => $display ) {
						echo '<option'.(($field['currentValue'] == $value) ? ' selected' : '').' value="'.$value.'">'.$display.'</option>';
					}
				echo '</select>';
			break;
			
			case 'editor' : 
				wp_editor( $value, $field['id'], array('textarea_name'=>$field['name']) );
				echo "<script>
				var $ = jQuery;
				$(document).ready(function(){
					// remove existing editor instance
					tinymce.execCommand('mceRemoveEditor', true, '".$field['id']."');

					// init editor for newly appended div
					var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ '".$field['id']."' ] );
					try { tinymce.init( init ); } catch(e){}
				});
				</script>";
			break;
			case 'taxonomy_select' : 
				echo '<select name="'.$field['name'].'" id="'.$field['id'].'">';
					echo '<option value="">إختر</option>';
					foreach( get_categories( ['taxonomy'=>$field['taxonomy'],'hide_empty'=>0] ) as $value => $display ) {
						echo '<option'.(($field['currentValue'] == $value) ? ' selected' : '').' value="'.$value.'">'.$display->name.'</option>';
					}
				echo '</select>';
			break;
			case 'TaxonomyChackBox' : 
				$k=0;foreach ($field['options'] as $value => $display) {$k++;
					$idCheckbox = $field['id'].$k;
					echo '<div class="TaxonomyChackBox" for="'.$idCheckbox.'">';
					echo '<input'.((in_array($value, (is_array($field['currentValue'])) ? $field['currentValue'] : array())) ? ' checked' : '').' type="checkbox" value="'.$value.'" name="'.$field['name'].'[]" id="'.$idCheckbox.'" />';
					echo '<span></span>';
					echo '<em>'.$display.'</em>';
					echo '</div>';
				}
			break;
			default: break;
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	echo '</label>';
}