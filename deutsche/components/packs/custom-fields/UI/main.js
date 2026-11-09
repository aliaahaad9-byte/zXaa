$(document).ready(function(){


	if ($('.all-fields').length && $('theme-widget-stack').length == 0 ) {	
		$('.all-values').each(function(){
			var tp = $(this).find('.all-fields').height() - 44;
			$(this).find('.add-field').css('top',tp)
		})

	}
	// ##
		$('.pinyhis').click(function(){
			var id = $(this).data('id');
			var pin = $(this).data('pin');
			if(pin !== 'true'){
				$(this).attr('aria-pressed','true');
				$(this).attr('data-pin',true);
			}else{
				$(this).attr('aria-pressed','false');
				$(this).attr('data-pin',false);

			}
			$.ajax({
				type:'POST',
				url:HomeURL+'/wp-admin/admin-ajax.php',
				data:{
					action:'pinned',
					id:id,
					pin:pin
				},
				success:function(data){

				}
			})
		})
	// ###

	$('body').on('keyup', '.Ptype--search--box', function(e){
		if($(this).find('input').val() !== ''){
			$(this).find('.Ptype-list').show();
			var that = $(this);
			$.ajax({
				url:HomeURL+'/wp-admin/admin-ajax.php',
				data:{"action":'post_search', "s":$(this).find('input').val(),'ptype':that.data('ptype'),'field_id':that.data('field_id')},
				type:'POST',
				success: function(msg) {
					that.find('.Ptype-list').html(msg);
				},
				error: function() {
					alert(errorAjax);
				}
			});
		}else{
			$(this).find('.Ptype-list').hide();
		}
	});	
	$('body').on('click', '.Ptype--tax--list li', function(e){
			$(this).find('.Ptype-list').show();
			var parent = $(this).closest('.Ptype--search--box');
			$.ajax({
				url:HomeURL+'/wp-admin/admin-ajax.php',
				data:{"action":'post_search_tax','ptype':parent.data('ptype'),'field_id':parent.data('field_id'),'term':$(this).data('term')},
				type:'POST',
				success: function(msg) {
					parent.find('.Ptype-list-value').prepend(msg);
				},
				error: function() {
					alert(errorAjax);
				}
			});
	});

	$('body').on('click', '.Ptype-list li', function(e){
		var parent = $(this).parent();
		clone  = $(this).clone();
		clone.append('<div class="close"></div>')
		id= $(this).data('id');
		parent.find('li[data-i="'+id+'"]').hide();
		parent.find('li[data-i="'+id+'"]').remove('input');
		parent.siblings('.Ptype-list-value').append(clone);
		$(this).remove();
		$('.Ptype-list').html('');
		$('.Ptype-list').hide();
	});

	$('body').on('click', '.Ptype--search--box li .close', function(e){
		 $(this).parent().remove();

	});	

	$(document).mouseup(function(e){
	    var dropdown = $(".Ptype--tax--list");
	    if (!dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
	        $('.Ptype--tax--list').removeClass('active');
	    }
	});

	$('tag').click(function(){
	    $(this).addClass('active').siblings().removeClass('active');
	    var ids = '';
	    $('tag.active').each(function(){
	        ids+= $(this).data('id')+','
	    });
	    $('#tags_order').val(ids)
	})
	$('categories').click(function(){
	    $(this).addClass('active').siblings().removeClass('active');
	    var ids = '';
	    $('categories.active').each(function(){
	        ids+= $(this).data('id')+','
	    });
	    $('#categories').val(ids)
	})

 
/* Start  Upgrade Edits  */
	$('.add-field').click(function(){
	
		var count = $(this).closest('.all-values').find('.repeatable').length;
		var id = $(this).closest('.all-values').find('.first-element').attr('id').split('_yc_')[0];
		var el = $(this).closest('.all-values').find('.first-element').clone();
		el.attr('id',id+'_yc_'+count);
		el.val('');
		el.removeClass('first-element');
    	$(this).closest('.all-values').find('.all-fields').append('<div class="el-field"><i class="fa fa-times"></i></div>');

    	$(this).closest('.all-values').find('.el-field:last-child').prepend(el);
		var tp = $(this).closest('.all-values').find('.all-fields').height() - 44;
		$(this).css('top',tp);

	
    });

/*    $('body').click(function(){
    	$('fonts--box').removeClass('open')
    }) */
     $('font--icons input').focus(function(e){
     	e.stopPropagation();
    	$('fonts--box').addClass('open')
    }) 
    $('theme-widget-stack').click(function(e){
    	e.stopPropagation();
    })

	function OembedFrame(el,value){
		if( value.trim() == '' ){
			el.closest('[class*="field-inner"]').find('textarea').html('');
			el.closest('[class*="field-inner"]').find('youtube--code').remove()
			el.closest('label').find('youtube--code').remove()
		}else{

			if (value.includes('youtube')) {
				YoutubeEmbed(el,value);
			}else if (value.includes('instagram')) {
				InstagramEmbed(el,value);
			} else if (value.includes('twitter')) {
				TwitterEmbed(el,value);
			}


		}

	}

	$('.select-tabs').change(function(){
		$(this).next().find('#'+$(this).val().replace(' ','')).show().siblings().hide()
	})
	
	function TwitterEmbed(el,value){
		el.closest('[class*="field-inner"]').find('youtube--code').remove()
		el.closest('label').find('youtube--code').remove()
		var src = value.split('status/')[1];

		var embedCode = `<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Sunsets don&#39;t get much better than this one over <a href="https://twitter.com/GrandTetonNPS?ref_src=twsrc%5Etfw">@GrandTetonNPS</a>. <a href="https://twitter.com/hashtag/nature?src=hash&amp;ref_src=twsrc%5Etfw">#nature</a> <a href="https://twitter.com/hashtag/sunset?src=hash&amp;ref_src=twsrc%5Etfw">#sunset</a> <a href="http://t.co/YuKy2rcjyU">pic.twitter.com/YuKy2rcjyU</a></p>&mdash; US Department of the Interior (@Interior) <a href="https://twitter.com/Interior/status/${src}?ref_src=twsrc%5Etfw">May 5, 2014</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></blockquote>`;
		
		el.after(`<youtube--code class="twitter"><remove--youtube class="fa-solid fa-xmark"></remove--youtube>${embedCode}</youtube--code>`)
		
		el.closest('[class*="field-inner"]').find('textarea').html(embedCode);
		el.closest('label').find('textarea').html(embedCode);

	}

	function InstagramEmbed(el,value){
		el.closest('[class*="field-inner"]').find('youtube--code').remove()
		el.closest('label').find('youtube--code').remove()


		var src = value.split('p/')[1];
		src = src.split('/')[0];
		src = 'https://www.instagram.com/p/'+src+'/embed';
		var embedCode = `<iframe width="100%" height="800" src="${src}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write;encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
		el.after(`<youtube--code class="instagram"><remove--youtube class="fa-solid fa-xmark"></remove--youtube>${embedCode}</youtube--code>`)
		el.closest('[class*="field-inner"]').find('textarea').html(embedCode);
		el.closest('label').find('textarea').html(embedCode);

	}
	function YoutubeEmbed(el,value){
		el.closest('[class*="field-inner"]').find('youtube--code').remove()
		el.closest('label').find('youtube--code').remove()
		
		if ( value.split('?v=')[1].length) {
			var src = 'https://www.youtube.com/embed/'+value.split('?v=')[1];
			var embedCode = `<iframe width="100%" height="450" src="${src}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; 
							encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
			el.after(`<youtube--code class="youtube"><remove--youtube class="fa-solid fa-xmark"></remove--youtube>${embedCode}</youtube--code>`)
			el.closest('[class*="field-inner"]').find('textarea').html(embedCode)
			el.closest('label').find('textarea').html(embedCode);
		}
	}


	$('body').on('click','remove--youtube',function(){
		$(this).closest('[class*="field-inner"]').find('input').val('')
		$(this).closest('[class*="field-inner"]').find('textarea').val('')
		$(this).closest('label').find('input').val('')
		$(this).closest('label').find('textarea').val('')
		$(this).parent().remove();
	})

	$('.add-youtube-link').keyup(function(){
		OembedFrame($(this),$(this).val())
	});

	$('.add-youtube-link').bind("paste", function(e){

		OembedFrame($(this),$(this).val())
	} );



	$('body').on('click','.el-field i',function(){
		$(this).parent().remove();
		var st = 1;
		$('.el-field').each(function(){
			var id = $(this).find('>*').attr('id').split('_yc_')[0];
			$(this).find('>*').attr('id',id+'_yc_'+st);
			st = st + 1;
		})
	})


	$('all-terms term').on('click',function(){
		$(this).closest('[class*="field-inner"]').find('.choose-fonts').val($(this).html())
	})

	$('.search-terms input').on('keyup',function(){
	   var searched = $(this).val().toLowerCase()
      	$(this).closest('.terms-container').find('term').each(function(){
	        if( $(this).text().toLowerCase().includes(searched) > 0 ){
	            $(this).css('order','1')
	       }else {
	        $(this).css('order','2')
	        }
		})
	})

	$('font--icons input').keyup(function(){
	

		var icons = $(this).closest('fonts--box').find('all-icons')
		icons.html('<loader class="lds-ellipsis"><div></div><div></div><div></div><div></div></loader>')
		$.ajax({
			type:'POST',
			url:$(this).data('url'),
			data:{
				action:'get_icon',
				s:$(this).val(),
				font:$(this).data('font')
			},
			success:function(data){
				icons.html(data)
			}
		})
	})

	$('check--tab').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		$($(this).data('id')).show().siblings().hide()
	})

	$('hour, minute, ampm-options').click(function(){
		$(this).parent().prev().val($(this).text())
	})

	$('.choose-date').click(function(){
		$('danger').remove()
		var hour = $(this).closest('time--box').find('.choose-hour input').val();
		var min = $(this).closest('time--box').find('.choose-min input').val();
		var am = $(this).closest('time--box').find('.choose-am-pm input').val();
		if (hour != '' && min != '' && am != '') {

			$(this).closest('[class*="field-inner"]').find('.show-time').val(am+' '+hour+':'+min);
			$(this).closest('label').find('.show-time').val(am+' '+hour+':'+min).change();
			$(this).parent().removeClass('open')
		}else {
			$(this).after('<danger>لم يتم تحديد الوقت بشكل سليم</danger>')
		}
	})

	$('.show-time').focus(function(){
		$(this).next().addClass('open')
	})	
	$('.show-time').on('click',function(e){
		e.stopPropagation()
	})
	$('body').on('click',function(){
		$('time--box').removeClass('open')
	})

	$('time--box').on('click',function(e){
		e.stopPropagation()
	})

	$('.choose-fonts').focus(function(){
		$('fonts--box').removeClass('open')
		$(this).next('fonts--box').addClass('open')
	})

	$('body').on('click','all-icons div',function(){
		$(this).closest('font--icons').find('input').val($(this).attr('class'))
	})
	$('.remove-font-icons').on('click',function(){
		$(this).parent().removeClass('open')
	})

	$('body').on('click','all-icons div',function(){
		$(this).closest('font--icons').find('input[type="hidden"]').val($(this).attr('class'));
		$(this).closest('font--icons').find('.choose-fonts').val($(this).attr('class').replace('fa ','').replace('fa-','').replace('ion-ios-','')).change();
		$(this).closest('font--icons').find('icons').html('<div class="'+$(this).attr('class')+'"></div>')
	})
	 
 	$('body').on('click','all-icons lord-icon',function(){
		$(this).closest('font--icons').find('input').val($(this).attr('data-id')).change();
		$(this).closest('font--icons').find('icons').html('<lord-icon src="https://cdn.lordicon.com/'+$(this).attr('data-id')+'.json"  trigger="loop" style="width:80px;height:80px"></lord-icon>')
	})
	/*  END Upgrade Edits  */
	$('#misc-publishing-actions').append('<input type="hidden" name="apbupdate" value="1" />');
	$('body').on('click', 'ul.APBLayouts > li', function(e){
		$('#'+$(this).data('fields')).val($(this).data('layoutval'));
		var APBBuilderParent = $(this).parent().parent().find('.APBLayoutsBuilder');
		$(this).parent().find('li').removeClass('layout-current');
		$(this).addClass('layout-current');
		APBBuilderParent.html('<div class="APBLoader"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve"> <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946 s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634 c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"></path> <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0 C22.32,8.481,24.301,9.057,26.013,10.047z"> <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.5s" repeatCount="indefinite"></animateTransform> </path> </svg></div>');
		$.ajax({
			url:HomeURL+'/wp-admin/admin-ajax.php',
			data:{"action":'APBLayoutsBuilder', "fields":$(this).data('fields'), "numb":$(this).parent().parent().data('numb'), "layout":$(this).data('layout')},
			type:'POST',
			success: function(msg) {
				$('body, html').animate({"scrollTop":APBBuilderParent.offset().top - ($(window).height() / 2) + 100});
				APBBuilderParent.html(msg);
			},
			error: function() {
				alert(errorAjax);
			}
		});
	});
	if (!$('.AddMoreGroup').hasClass('yts')) {
		
		$('body').on('click', '.AddMoreGroup', function(e){
			var APBParentObject = $(this).parent().parent();
			var APBParentParent = $(this).parent();
			$.ajax({
				url:HomeURL+'/wp-admin/admin-ajax.php',
				data:{"action":'APBAddGroupFields', "metabox":$(this).data('metabox'), "numb":$(this).data('numb'), "group":$(this).data('group')},
				type:'POST',
				success: function(msg) {
					APBParentObject.after(msg);
					APBParentParent.remove();
					$('.LayoutsBuilderFooter + .LayoutsBuilderFooter').remove();
				},
				error: function() {
					alert(errorAjax);
				}
			});
		});
	}
	$('body').on('click', '.AddMoreGroup.yts', function(e){
		e.preventDefault();
		console.log('dddd')
		var YTSParentObject = $(this).parent().parent();
		var YTSParentParent = $(this).parent();
		$.ajax({
			url:HomeURL+'/wp-admin/admin-ajax.php',
			data:{"action":'YTSAddGroupFields', "metabox":$(this).data('metabox'), "numb":$(this).data('numb'), "group":$(this).data('group')},
			type:'POST',
			success: function(msg) {
				YTSParentObject.after(msg);
				YTSParentParent.remove();
				$('.LayoutsBuilderFooter + .LayoutsBuilderFooter').remove();
			},
			error: function() {
				alert(errorAjax);
			}
		});
	});
	$('body').on('click', '.AddMoreLayout', function(e){
		var APBParent = $(this).parent();
		$.ajax({
			url:HomeURL+'/wp-admin/admin-ajax.php',
			data:{"action":'APBAddLayoutBuilder', "numb":$(this).data('numb'), "metabox":$(this).data('metabox')},
			type:'POST',
			success: function(msg) {
				APBParent.parent().after(msg);
				APBParent.remove();
			},
			error: function() {
				alert(errorAjax);
			}
		});
	});
});
function NextItem(el) {
	var $Parent=$(el).parent().parent();
	if( $Parent.prev().hasClass('APBMainLayout') ) {
		$Parent.insertBefore($Parent.prev());
	}
}
function PrevItem(el) {
	var $Parent=$(el).parent().parent();
	if( $Parent.next().hasClass('APBMainLayout') ) {
		$Parent.insertAfter($Parent.next());
	}
}
function RemoveGroupField(el) {
	$(el).parent().parent().remove();
}

	
	// ###
	
	$('body').on('click', '.Ptype--search--box li .close', function(e){
		 $(this).parent().remove();

	});	
