<?php
class APBFieldsTypes extends APBFields {
	private $field;
	private $id;
	private $value;
	public function Field(
		$field,
		$name, 
		$id, 
		$hasparent, 
		$isgroup='no', 
		$layout='', 
		$numb=0, 
		$moregroup='', 
		$MetaboxID='', 
		$taxonomy=false
		) {
		$value = '';
		if(  /*$taxonomy == true */ isset($_GET['tag_ID']) ) {
			if( (new APBFields)->type == 'edit' ) {
				if( $hasparent == '' ){
					if( $isgroup > 0 ){
						$isgroup = $isgroup - 1;
						$parent  = get_term_meta($this->post, $moregroup, true);
						if( isset($parent[$isgroup][$field['id']]) ) {
							$value = $parent[$isgroup][$field['id']];
						}
					}else {
						$value  = get_term_meta($this->post, $name, true);
					}
				}else {
					$parent = get_term_meta($this->post, $hasparent, true);
					if (isset($parent[$numb][$layout][$field['id']])) {
						$value = $parent[$numb][$layout][$field['id']];
					}else {
						$value = '';
					}
				}
			}else {
				$value = '';
			}
		}else {
			if( (new APBFields)->type == 'edit' ) {
				if( $hasparent == '' ){
					if( $isgroup > 0 ){
						$isgroup = $isgroup - 1;
						$parent  = get_post_meta($this->post, $moregroup, true);
						if( isset($parent[$isgroup][$field['id']]) ) {
							$value = $parent[$isgroup][$field['id']];
						}
					}else {
						$value  = get_post_meta($this->post, $name, true);
						//print_r(array($name));
					}
				}else {
					$parent = get_post_meta($this->post, $hasparent, true);
					if (isset($parent[$numb][$layout][$field['id']])) {
						$value = $parent[$numb][$layout][$field['id']];
					}else {
						$value = '';
					}
				}
			}else {
				$value = '';
			}
		}
		if( $field['type'] == 'text' ) {
			echo '<div class="apb-field-inner">';
			$this->Text($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'textarea' ) {
			echo '<div class="apb-field-inner">';
			$this->Textarea($value, $field, $name, $id);
			echo '</div>';			
		}else if( $field['type'] == 'number' ) {
			echo '<div class="apb-field-inner">';
			$this->Number($value, $field, $name, $id);
			echo '</div>';				
		}else if( $field['type'] == 'email' ) {
			echo '<div class="apb-field-inner">';
			$this->Email($value, $field, $name, $id);
			echo '</div>';			
		}else if( $field['type'] == 'tabs' ) {
			echo '<div class="apb-field-inner">';
			$this->Tabs($value, $field, $name, $id);
			echo '</div>';		
		}else if( $field['type'] == 'oembed' ) {
			echo '<div class="apb-field-inner">';
			$this->Oembed($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'textarea_code' ) {
			echo '<div class="apb-field-inner">';
			$this->TextareaCode($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'select' ) {
			echo '<div class="apb-field-inner">';
			$this->Select($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'date' ) {
			echo '<div class="apb-field-inner">';
			$this->Date($value, $field, $name, $id);
			echo '</div>';		
		}else if( $field['type'] == 'time' ) {
			echo '<div class="apb-field-inner">';
			$this->Time($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'colorpicker' ) {
			echo '<div class="apb-field-inner">';
			$this->ColorPicker($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'radio' ) {
			echo '<div class="apb-field-inner">';
			$this->Radio($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'taxonomy_select' ) {
			echo '<div class="apb-field-inner">';
			$this->TaxonomySelect($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'taxonomy_radio' ) {
			echo '<div class="apb-field-inner">';
			$this->TaxonomyRadio($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'taxonomy_checkbox' ) {
			echo '<div class="apb-field-inner">';
			$this->TaxonomyCheckbox($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'checkbox' ) {
			echo '<div class="apb-field-inner">';
			$this->Checkbox($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'file' ) {
			echo '<div class="apb-field-inner">';
			$this->File($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'file_list' ) {
			echo '<div class="apb-field-inner">';
			$this->FileList($value, $field, $name, $id);
			echo '</div>';		
		}else if( $field['type'] == 'icons' ) {
			echo '<div class="apb-field-inner">';
			$this->Icons($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'editor' ) {
			echo '<div class="apb-field-inner">';
			$this->Editor($value, $field, $name, $id);
			echo '</div>';
		}else if( $field['type'] == 'group' ) {
			$this->Group($value, $field, $name, $id, $MetaboxID);
		}else if( $field['type'] == 'title' ) {
			echo '<div class="FieldTitle">'.$field['name'].'</div>';
		}
	}


	public function Oembed($value, $field, $name, $id) {
		$field_att = '';
		if (isset($field['attributes'])) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}

		$oembed_link = '';

		if (isset($_GET['post'])) {

			if ($value != '') {

				preg_match('/src="([^"]+)"/', $value, $match);
				$url = $match[1];

				if (strpos($url, 'youtube') !== false) {

					$id_code = explode('embed/', $url)[1];
					$oembed_link = 'https://www.youtube.com/watch?v='.$id_code;

				}else if(strpos($url, 'instagram') !== false){
					$id_code = explode('p/', $url)[1];
					$id_code = explode('/', $id_code)[0];
					$oembed_link = 'https://www.instagram.com/p/'.$id_code.'';
				}else if(strpos($url, 'twitter') !== false){
					$id_code = explode('twitter.com/Interior/status/', $value)[1];
					$id_code = explode('?ref', $id_code)[0];
					$oembed_link = 'https://twitter.com/Interior/status/'.$id_code;
				}

			}

		}

		echo '<textarea style="display:none" name="'.$name.'" class="youtube-link"  id="'.$id.'">';
			echo $value;
		echo '</textarea>';

		echo '<input type="text" value="'.$oembed_link.'" name="'.$name.'_yc" class="add-youtube-link" '.$field_att.'  />';
		if ($value != '') {
			echo '<youtube--code>';
				echo '<remove--youtube class="fa-solid fa-xmark"></remove--youtube>';
				echo $value;
			echo '</youtube--code>';
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}

	public function Text($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		if (isset($field['repeatable'])) {
			echo '<div class="all-values">';

				echo '<div class="all-fields">';
					if (empty($value)) {
						echo '<input type="text" class="repeatable first-element" value="'.$value.'" name="'.$name.'[]" '.$field_att.' id="'.$id.'_yc_0" />';
					}else {
						foreach ($value as $i =>  $v) {
							echo $i > 0 ? '<div class="el-field">' : '';
								echo '<input class="repeatable '.($i == 0 ? 'first-element' : '' ).'"  type="text" value="'.$v.'" name="'.$name.'[]" '.$field_att.' id="'.$id.'_yc_'.$i.'" />';
							echo $i > 0 ? '<i class="fa fa-times"></i></div>' : '';
						}
					}
				echo '</div>';

				echo '<div class="add-field"><i class="fa fa-plus"></i></div>';

			echo '</div>';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}else{
			echo '<input type="text" value="'.$value.'" name="'.$name.'" '.$field_att.' id="'.$id.'" />';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}
	}	
	public function Email($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		if (isset( $field['repeatable'] ) && $field['repeatable'] == true ) {
			echo '<div class="all-values">';

				echo '<div class="all-fields">';
					if (empty($value)) {
						echo '<input type="email" class="repeatable first-element" value="'.$value.'" name="'.$name.'[]" '.$field_att.' id="'.$id.'_yc_0" />';
					}else {
						foreach ($value as $i =>  $v) {
							echo $i > 0 ? '<div class="el-field">' : '';
								echo '<input class="repeatable '.($i == 0 ? 'first-element' : '' ).'"  type="email" value="'.$v.'" name="'.$name.'[]" '.$field_att.' id="'.$id.'_yc_'.$i.'" />';
							echo $i > 0 ? '<i class="fa fa-times"></i></div>' : '';
						}
					}
				echo '</div>';

				echo '<div class="add-field"><i class="fa fa-plus"></i></div>';

			echo '</div>';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}else{
			echo '<input type="email" value="'.$value.'" name="'.$name.'" '.$field_att.' id="'.$id.'" />';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}
	}	
	public function Number($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		if (isset( $field['repeatable'] ) && $field['repeatable'] == true ) {
			echo '<div class="all-values">';

				echo '<div class="all-fields">';
					if (empty($value)) {
						echo '<input type="number" class="repeatable first-element" value="'.$value.'" name="'.$name.'[]" '.$field_att.' id="'.$id.'_yc_0" />';
					}else {
						foreach ($value as $i =>  $v) {
							echo $i > 0 ? '<div class="el-field">' : '';
								echo '<input class="repeatable '.($i == 0 ? 'first-element' : '' ).'"  type="number" value="'.$v.'" name="'.$name.'[]" '.$field_att.' id="'.$id.'_yc_'.$i.'" />';
							echo $i > 0 ? '<i class="fa fa-times"></i></div>' : '';
						}
					}
				echo '</div>';

				echo '<div class="add-field"><i class="fa fa-plus"></i></div>';

			echo '</div>';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}else{
			echo '<input type="number" value="'.$value.'" name="'.$name.'" '.$field_att.' id="'.$id.'" />';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}
	}
	public function Textarea($value, $field, $name, $id) {
		$height = '';
		if( isset($field['height']) ) {
			$height = 'style="height:'.$field['height'].'px"';
		}
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		if (isset( $field['repeatable'] ) && $field['repeatable'] == true ) {
			echo '<div class="all-values">';

				echo '<div class="all-fields">';
					if (empty($value)) {
						echo '<textarea  class="repeatable first-element" '.$height.' name="'.$name.'[]" id="'.$id.'_yc_0" '.$field_att.'>'.$value.'</textarea>';
					}else {
						foreach ($value as $i =>  $v) {
							echo $i > 0 ? '<div class="el-field">' : '';
								echo '<textarea  class="repeatable '.($i == 0 ? 'first-element' : '' ).'"  '.$height.' name="'.$name.'[]" id="'.$id.'_yc_0" '.$field_att.'>'.$v.'</textarea>';
							echo $i > 0 ? '<i class="fa fa-times"></i></div>' : '';
						}
					}
				echo '</div>';

				echo '<div class="add-field"><i class="fa fa-plus"></i></div>';

			echo '</div>';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}else{
			echo '<textarea '.$height.' name="'.$name.'" id="'.$id.'" '.$field_att.'>'.$value.'</textarea>';
			echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		}

	}
	public function Icons($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		if (isset( $field['library'] )) {	
			$library = $field['library'];
			echo '<font--icons>';
				echo '<input type="text" data-url="'.admin_url('admin-ajax.php' ).'" data-font="'.$library.'" value="'.$value.'" name="'.$name.'" '.$field_att.' id="'.$id.'" />';

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

				if ($value != '') {
					echo '<icons>';
					if ($library !== 'lordicons') {
						echo '<div class="'.$value.'"></div>';
					}else if( $library == 'lordicons') {
						echo '<lord-icon src="https://cdn.lordicon.com/'.$value.'.json"  trigger="loop" style="width:80px;height:80px"></lord-icon>';
					}
					echo '</icons>';
				}
			echo '</font--icons>';
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}	
	public function TextareaCode($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		echo '<textarea class="CodePreview" name="'.$name.'" id="'.$id.'" '.$field_att.'>'.$value.'</textarea>';
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function Select($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		echo '<select '.( isset($field['fields']) ? ' class="select-tabs"' : '' ).' name="'.$name.'" id="'.$id.'" '.$field_att.'>';
		foreach ($field['options'] as $v => $option) {
			echo '<option'.(($value == $v) ? ' selected' : '').' value="'.$v.'">'.$option.'</option>';
		}
		echo '</select>';
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		if( isset($field['onchange']) ) {
			echo '<script>';
			echo 'jQuery(window).load(function(){';
			echo 'jQuery("#'.$id.'").change(function(){';
				foreach ($field['onchange'] as $k8 => $v8) {
					echo 'if( $(this).val() == "'.$k8.'" ) {';
					echo 'jQuery(this).parent().parent().parent().find(".apb-field-'.$v8.'").show();';
					echo '}else {';
					echo 'jQuery(this).parent().parent().parent().find(".apb-field-'.$v8.'").hide();';
					echo '}';
				}
			echo '});';
			foreach ($field['onchange'] as $k8 => $v8) {
				if( $value == $k8 ) {
				}else {
					echo 'jQuery("#'.$id.'").parent().parent().parent().find(".apb-field-'.$v8.'").hide();';
				}
			}
			echo '});';
			echo '</script>';
		}

		if (isset($field['fields'])) {
			echo '<select--fields>';
				foreach ($field['fields'] as $k => $arr) {
					echo '<div id="'.str_replace(' ','',$k).'" '.( $k == $value ? 'style="display:block"' : '').'>';
						foreach ($arr as $f) {

							if( !empty($f) ) {
								echo '<div class="apb-field apb-hook apb-field-'.$f['id'].' apb-type-'.$f['type'].'">';
								if( $f['type'] == 'title' ) {
									$name = $parentname.'['.$k.']['.$f['id'].']';
									$id = $parentname.'_'.$k.'_'.$f['id'];
									$this->Field($f, $name, $id, 0, ((INT) $k + 1), '', '', $parentname, $MetaboxID);
								}else {
									$name = $parentname.'['.$k.']['.$f['id'].']';
									$id = $parentname.'_'.$k.'_'.$f['id'];
									echo '<label for="'.$parentname.'_'.$f['id'].'">'.$this->ARorEN($f['name'], $f).'</label>';
									$this->Field($f, $name, $id, 0, ((INT) $k + 1), '', '', $parentname, $MetaboxID);
								}
								echo '</div>';
							}

						}
					echo '</div>';
				}
			echo '</select--fields>';
		}

	}
	public function Time($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}

		echo '<input type="text" value="'.$value.'" class="show-time" name="'.$name.'" '.$field_att.' id="'.$id.'" />';
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
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}	
	public function Date($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		//echo '<input type="text" value="'.$value.'"  '.$field_att.' data-language="en" class="DatePreview" data-time="'.(($field['time'] == true) ? 'true' : 'false').'" data-format="'.((isset($field['format'])) ? $field['format'] : 'd-m-Y H:i:s').'" name="'.$name.'" id="'.$id.'" />';
		echo '<input type="date" value="'.$value.'" name="'.$name.'" '.$field_att.' id="'.$id.'" />';
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function ColorPicker($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}
		echo '<input type="text" value="'.$value.'"  '.$field_att.' class="ColorViewer" name="'.$name.'" id="'.$id.'" />';
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
		
	}
	public function Radio($value, $field, $name, $id) {

		if (isset($field['images']) && $field['images'] == true) {
			$k=0;
			foreach ($field['options'] as $v => $opt) {$k++;
				$idCheckbox = $id.$k;
				echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';
					echo '<input'.(($v == $value) ? ' checked' : '').' type="radio" value="'.$v.'" name="'.$name.'" id="'.$idCheckbox.'" />';
					echo '<span></span>';
					echo '<img src="'.$opt.'" />';
				echo '</label>';
			}
		}else{

			$k=0;foreach ($field['options'] as $v => $opt) {$k++;
				$idCheckbox = $id.$k;
				echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
					echo '<input'.(($v == $value) ? ' checked' : '').' type="radio" value="'.$v.'" name="'.$name.'" id="'.$idCheckbox.'" />';
					echo '<span></span>';
					echo '<em>'.$opt.'</em>';
				echo '</label>';
			}
		}
		

		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function TaxonomySelect($value, $field, $name, $id) {
		$taxonomy = 'category';
		if( isset($field['taxonomy']) ) {
			$taxonomy = $field['taxonomy'];
		}
		$args = array('Tax'=>$taxonomy);
		if( isset($field['field']) ) {
			$args['field'] = $field['field'];
		}
		if( isset($field['number']) ) {
			$args['number'] = $field['number'];
		}
		if( !isset($field['placeholder']) ) {
			$field['placeholder'] = 'إختر';
		}

		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}


		if (isset($field['searchbox']) && $field['searchbox'] == true ) {
			echo '<taxonomy--search--box>';
				echo '<input type="text"  name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$field_att.'>';
				echo '<all-terms>';
					foreach ($this->TaxonomyList($args) as $v => $option) {
					echo '<term>'.$option.'</term>';
				}
				echo '</all-terms>';
			echo '</taxonomy--search--box>';
		}else{

			echo '<select name="'.$name.'" id="'.$id.'" '.$field_att.'>';
			echo '<option'.(($value == '') ? ' selected' : '').' value="">'.$field['placeholder'].'</option>';
				foreach ($this->TaxonomyList($args) as $v => $option) {
					echo '<option'.(($value == $v) ? ' selected' : '').' value="'.$v.'">'.$option.'</option>';
				}
			echo '</select>';
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function TaxonomyRadio($value, $field, $name, $id) {
		$taxonomy = 'category';
		if( isset($field['taxonomy']) ) {
			$taxonomy = $field['taxonomy'];
		}
		$args = array('Tax'=>$taxonomy);
		if( isset($field['field']) ) {
			$args['field'] = $field['field'];
		}
		if( isset($field['number']) ) {
			$args['number'] = $field['number'];
		}
		$k=0;foreach ($this->TaxonomyList($args) as $v => $opt) {$k++;
			$idCheckbox = $id.$k;
			echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
				echo '<input'.(($v == $value) ? ' checked' : '').' type="radio" value="'.$v.'" name="'.$name.'" id="'.$idCheckbox.'" />';
				echo '<span></span>';
				echo '<em>'.$opt.'</em>';
			echo '</label>';
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function TaxonomyCheckbox($value, $field, $name, $id) {
		$taxonomy = 'category';
		if( isset($field['taxonomy']) ) {
			$taxonomy = $field['taxonomy'];
		}
		$args = array('Tax'=>$taxonomy);
		if( isset($field['field']) ) {
			$args['field'] = $field['field'];
		}
		if( isset($field['number']) ) {
			$args['number'] = $field['number'];
		}
		$k=0;
		foreach ($this->TaxonomyList($args) as $v => $opt) {
			$k++;
			$idCheckbox = $id.$k;
			echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
				echo '<input'.((in_array($v, (is_array($value)) ? $value : array())) ? ' checked' : '').' type="checkbox" value="'.$v.'" name="'.$name.'[]" id="'.$idCheckbox.'" />';
				echo '<div class="toggle"></div>';
				echo '<em>'.$opt.'</em>';
			echo '</label>';
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function Tabs($value, $field, $name, $id) {

		if (isset($field['tabs'])) {
			echo '<checkbox--tabs>';
				foreach ($field['tabs'] as $i =>  $k ) {
					echo '<check--tab '.( $i == 0 ? 'class="active"' :  ''  ).' data-id="#'.str_replace(' ','',$k).'">';
						echo $k;
					echo '</check--tab>';
				}
			echo '</checkbox--tabs>';

			echo '<checkbox--options>';
				$j = 0;
				foreach ($field['options'] as $key => $arr) {
					echo '<div '.( $j == 0 ? 'class="open"' :  ''  ).' id="'.str_replace(' ','',$key).'">';
						foreach ($arr as $f) {

							if( !empty($f) ) {
								echo '<div class="apb-field apb-hook apb-field-'.$f['id'].' apb-type-'.$f['type'].'">';
								if( $f['type'] == 'title' ) {
									$name = $parentname.'['.$k.']['.$f['id'].']';
									$id = $parentname.'_'.$k.'_'.$f['id'];
									$this->Field($f, $name, $id, 0, ((INT) $k + 1), '', '', $parentname, $MetaboxID);
								}else {
									$name = $parentname.'['.$k.']['.$f['id'].']';
									$id = $parentname.'_'.$k.'_'.$f['id'];
									echo '<label for="'.$parentname.'_'.$f['id'].'"><i class="fa fa-arrow-right-arrow-left"></i><span>'.$this->ARorEN($f['name'], $f).'</span></label>';
									$this->Field($f, $name, $id, 0, ((INT) $k + 1), '', '', $parentname, $MetaboxID);
								}
								echo '</div>';
							}

						}
					echo '</div>';
					$j++;
				}
			echo '</checkbox--options>';
		}


		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function Checkbox($value, $field, $name, $id) {

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
								$idCheckbox = $id.$k;
								echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';
								echo '<input'.((in_array($v, (is_array($value)) ? $value : array())) ? ' checked' : '').' type="checkbox" value="'.$v.'" name="'.$name.'[]" id="'.$idCheckbox.'" />';
								echo '<span></span>';
								echo '<img src="'.$opt.'" />';
								echo '</label>';
							}
						}else{
							foreach ($arr as $v => $opt) {
								$k++;
								$idCheckbox = $id.$k;
								echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
									echo '<input'.((in_array($v, (is_array($value)) ? $value : array())) ? ' checked' : '').' type="checkbox" value="'.$v.'" name="'.$name.'[]" id="'.$idCheckbox.'" />';
									echo '<span></span>';
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
						$idCheckbox = $id.$k;
						echo '<label class="APBCheckLabel chooseImage" for="'.$idCheckbox.'">';
							echo '<input'.((in_array($v, (is_array($value)) ? $value : array())) ? ' checked' : '').' type="checkbox" value="'.$v.'" name="'.$name.'[]" id="'.$idCheckbox.'" />';
							echo '<span></span>';
							echo '<img src="'.$opt.'" />';
						echo '</label>';
					}

				}else {

					$k=0;
					foreach ($field['options'] as $v => $opt) {$k++;
						$idCheckbox = $id.$k;
						echo '<label class="APBCheckLabel" for="'.$idCheckbox.'">';
							echo '<input'.((in_array($v, (is_array($value)) ? $value : array())) ? ' checked' : '').' type="checkbox" value="'.$v.'" name="'.$name.'[]" id="'.$idCheckbox.'" />';
						echo '<div class="toggle"></div>';
						echo '</label>';
					}
				}

			}else {
				$selected = 'on';
				if( isset($field['val']) ) {
					$selected = $field['val'];
				}
				$value = (is_array($value)) ? '' : $value;
				echo '<label class="APBCheckLabel" for="'.$id.'">';
					echo '<input '.(($value == 'on') ? 'checked' : '').' type="checkbox" value="'.$selected.'" name="'.$name.'" id="'.$id.'" />';
				echo '<div class="toggle"></div>';
				echo '</label>';
			}

		}

		

		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function File($value, $field, $name, $id) {
		$field_att = '';
		if (isset( $field['attributes'] )) {
			foreach ($field['attributes'] as $key => $v) {
				$field_att.=' '.$key.'="'.$v.'" ';
			}
		}

		$id = str_replace(array('[', ']'), '_', $id);
		$id = str_replace('__', '_', $id);
		$button = ((is_rtl()) ? 'رفع ملف' : 'Upload file');
		echo '<input type="hidden" id="'.$id.'_id" />';
		echo '<input type="text" value="'.$value.'"  '.$field_att.' name="'.$name.'" id="'.$id.'" />';
		echo '<a href="javascript:void(0);" data-multiple="false" data-type="'.((isset($field['mime'])) ? $field['mime'] : 'image').'" data-field="#'.$id.'" data-name="'.$name.'" data-rlname="'.$field['name'].'" class="APBUploadButton">'.((isset($field['button'])) ? $field['button'] : $button).'</a>';
		$style='';
		if( empty($value) ) {$style='display:none;';}

		if ( strpos($value,'pdf') !== false  ) {
			echo '<div class="APBPreviewFile preview-file" id="'.$id.'_preview" style="'.$style.'">';
				echo '<i class="fa-solid fa-file-pdf"></i>';
				echo '<span>'.end(explode('/', $value)).'</span>';
			echo '</div>';

		}else if(strpos($value,'docx') !== false){
			echo '<div class="APBPreviewFile preview-file" id="'.$id.'_preview" style="'.$style.'">';
				echo '<i class="fa-solid fa-book"></i>';
				echo '<span>'.end(explode('/', $value)).'</span>';
			echo '</div>';
		}else if( strpos($value,'ppt') !== false){
			echo '<div class="APBPreviewFile preview-file" id="'.$id.'_preview" style="'.$style.'">';
				echo '<i class="fa-solid fa-presentation-screen"></i>';
				echo '<span>'.end(explode('/', $value)).'</span>';
			echo '</div>';
		}else{
			echo '<img class="APBPreviewFile" id="'.$id.'_preview" style="'.$style.'" src="'.$value.'" />';
		}

		echo '<a style="'.$style.'" href="javascript:void(0);" class="APBRemoveButton" data-multiple="false" id="'.$id.'_remove"><i class="fa fa-times"></i></a>';
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function FileList($value, $field, $name, $id) {
		$id = str_replace(array('[', ']'), '_', $id);
		$id = str_replace('__', '_', $id);
		$button = ((is_rtl()) ? 'رفع ملف' : 'Upload file');
		echo '<a href="javascript:void(0);" data-multiple="true" data-type="'.((isset($field['mime'])) ? $field['mime'] : 'image').'" data-field="#'.$id.'" data-name="'.$name.'" data-rlname="'.$field['name'].'" class="APBUploadButton">'.((isset($field['button'])) ? $field['button'] : $button).'</a>';
		echo '<div class="previewList" id="'.$id.'_preview">';
		foreach ((is_array($value)) ? $value : array() as $k => $url) {
			echo '<span><input type="hidden" name="'.$name.'['.$k.']" value="'.$url.'" /><em onClick="this.parentNode.remove();"><span></span><span></span></em>';
			if (strpos($url,'pdf') !== false ) {
				echo '<div class="sm-preveiew"><i class="fa-solid fa-file-pdf"></i><span>'.end(explode('/', $url)).'</span></div>';
			}else if(strpos($url,'docx') !== false ){
				echo '<div class="sm-preveiew"><i class="fa-solid fa-book"></i><span>'.end(explode('/', $url)).'</span></div>';
			} else if(strpos($url,'ppt') !== false ){
				echo '<div class="sm-preveiew"><i class="fa-solid fa-presentation-screen"></i><span>'.end(explode('/', $url)).'</span></div>';
			}else{
				echo '<img src="'.$url.'" />';
			}
			echo '</span>';
		}
		echo '</div>';
		$style='';
		if( empty($value) ) {$style='display:none;';}
		echo '<a style="'.$style.'" href="javascript:void(0);" class="APBRemoveButton" data-multiple="true" id="'.$id.'_remove"><i class="fa fa-times"></i></a>';
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function Editor($value, $field, $name, $id) {
		wp_editor( $value, $id, array('textarea_name'=>$name) );
		echo "<script>
		$(document).ready(function(){
			// remove existing editor instance
			tinymce.execCommand('mceRemoveEditor', true, '".$id."');

			// init editor for newly appended div
			var init = tinymce.extend( {}, tinyMCEPreInit.mceInit[ '".$id."' ] );
			try { tinymce.init( init ); } catch(e){}
		});
		</script>";
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
	public function Group($value, $field, $parentname, $id, $MetaboxID='', $original=0) {
		$i = ($original == 0) ? 0 : $original;
		if( !empty($value) and $original == 0 ) {
			foreach ($value as $k => $v) {
				echo '<div>';
				echo '<h2>'.$field['name'].' [<em>'.($i + 1).'</em>]<a href="javascript:void(0);" onClick="RemoveGroupField(this);" class="RemoveIT"></a></h2>';
				foreach (array_filter($field['fields']) as $f) {
					if( !empty($f) ) {
						echo '<div class="apb-field apb-hook apb-field-'.$f['id'].' apb-type-'.$f['type'].'">';
						if( $f['type'] == 'title' ) {
							$name = $parentname.'['.$k.']['.$f['id'].']';
							$id = $parentname.'_'.$k.'_'.$f['id'];
							$this->Field($f, $name, $id, '', ((INT) $k + 1), '', '', $parentname, $MetaboxID);
						}else {
							$name = $parentname.'['.$k.']['.$f['id'].']';
							$id = $parentname.'_'.$k.'_'.$f['id'];
							echo '<label for="'.$parentname.'_'.$f['id'].'">'.$this->ARorEN($f['name'], $f).'</label>';
							$this->Field($f, $name, $id, '', ((INT) $k + 1), '', '', $parentname, $MetaboxID);
						}
						echo '</div>';
					}
				}
				$i = $k + 1;
				echo '</div>';
			}
			echo '<div class="LayoutsBuilderFooter">';
				echo '<a href="javascript:void();" class="AddMoreGroup" data-numb="'.$i.'" data-group="'.$parentname.'" data-metabox="'.$MetaboxID.'">+ '.((is_rtl()) ? 'إضافة المزيد' : 'Add More').'</a>';
			echo '</div>';
		}else {
			echo '<div>';
			echo '<h2>'.$field['name'].' [<em>'.($i + 1).'</em>]<a href="javascript:void(0);" onClick="RemoveGroupField(this);" class="RemoveIT"></a></h2>';
			foreach ($field['fields'] as $field) {
				echo '<div class="apb-field apb-field-'.$field['id'].' apb-type-'.$field['type'].'">';
				if( $field['type'] == 'title' ) {
					$name = $parentname.'['.$i.']['.$field['id'].']';
					$id = $parentname.'_'.$i.'_'.$field['id'];
					(new APBFieldsTypes)->Field($field, $name, $id, '', ((INT) $i + 1), '', '', '', $MetaboxID);
				}else {
					$name = $parentname.'['.$i.']['.$field['id'].']';
					$id = $parentname.'_'.$i.'_'.$field['id'];
					echo '<label for="'.$parentname.'_'.$field['id'].'">'.$this->ARorEN($field['name'], $field).'</label>';
					(new APBFieldsTypes)->Field($field, $name, $id, '', ((INT) $i + 1), '', '', '', $MetaboxID);
				}
				echo '</div>';
			}
			echo '</div>';
			echo '<div class="LayoutsBuilderFooter">';
				echo '<a href="javascript:void();" class="AddMoreGroup" data-numb="'.($i + 1).'" data-group="'.$parentname.'" data-metabox="'.$MetaboxID.'">+ '.((is_rtl()) ? 'إضافة المزيد' : 'Add More').'</a>';
			echo '</div>';
		}
		echo isset($field['desc']) ? '<description>'.$field['desc'].'</description>' : '';
	}
}