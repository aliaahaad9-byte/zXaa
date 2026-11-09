var $ = jQuery;
$( document ).ready(function() {
	
	
    $('body').on("click",'[data-remove]',function(){
	   	var BTN;
	    BTN = $(this);
	    Removed = BTN.data('remove');
	    $('[data-client="'+Removed+'"]').fadeOut();
	    URLAjax = HomeURL+'/AjaxCenter/removepost/';
	    $.ajax({
	        url: URLAjax,
	        dataType: 'json',
	        type: 'POST',
	        data:{"Removed":Removed},
	        success: function(msg) {
	          
	        }
	    });
	});
	
	
	$(document).mouseup(function(e) {
        var container = $(".box-message>.message");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('open');
        }

	});
	$('body').on("click",'.box-message',function(w){
   		$('.message').addClass('open').siblings().removeClass('open');
	});
	
	
	
	
	// # UNIQID.
		var charstoformid = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
		var UniqID = function() {
		  var idlength = 10;
		    var uniqid = '';
		    for (var i = 0; i < idlength; i++) {
		      uniqid += charstoformid[Math.floor(Math.random() * charstoformid.length)];
		    }
		    return uniqid;
		}

	// # CREATE NEW KEY .
		function CreateNewKey(keyargs){
			var Key = UniqID();
			if( jQuery.inArray(Key, keyargs) !== -1 ){
				CreateNewKey(keyargs);
			}else{
				return Key;
			}
		}
		// # REMOVED ALERT
			function RemoveAlert(data) {
				var PopRemoverElement = '<div class="Popver--CoursesAlert">';
				  PopRemoverElement += '<div class="PopverAlertOverlay" onClick="$(this).parent().remove();"></div>';
				  PopRemoverElement += '<div class="PopverInnerElemnt">';
				    PopRemoverElement += '<div class="HeadAlert--Popvoer"><h2>'+data.headtitle+'</h2><span class="Remover--CoursesAlerts hoverable" onClick="$(this).parent().parent().parent().remove();"><i class="fa-solid fa-xmark"></i></span></div>';
				    PopRemoverElement += '<p class="ContentAlert--Popvoer">'+data.alertcontent+'</p>';
				    PopRemoverElement += '<div class="ALertConroller--Popvoer">';
				      PopRemoverElement += '<a href="#" onClick="$(this).parent().parent().parent().remove();" class="hoverable">تراجع</a>';
				      PopRemoverElement += '<a href="#" '+data.ConfirmAttrs+' class="hoverable AlertIsConfirm"> نعم </a>';
				    PopRemoverElement += '</div>';
				  PopRemoverElement += '</div>';
				PopRemoverElement += '</div>';
				$('body').append(PopRemoverElement);
			}
			$('[data-navs-actions="RemoveSelectAll"]').hide();
	// # How TO POSTS IS SELECTED.
			function HowToCheckBoxNow(key){
				var List,Queue;
				Queue = 0;
				List = [];

				$('.-ScrollerCenter[data-uniqid="'+key+'"] > .-contain-MiniBox.selected').each(function(e,v){Queue++;
					List.push($(v).data('post-id'));
				});
				if(Queue > 0){
					$('[data-navs-actions="RemoveSelectAll"]').css({"pointer-events":'auto', "opacity":'1'});
					$('[data-navs-actions="RemoveAllSelected"]').css({"pointer-events":'auto', "opacity":'1'});
					$('[data-navs-actions="RemoveSelectAll"]').show();
					$('[data-navs-actions="SelectAll"]').hide();
				}else{
					$('[data-navs-actions="RemoveSelectAll"]').css({"pointer-events":'none', "opacity":'0.5'});
					$('[data-navs-actions="RemoveAllSelected"]').css({"pointer-events":'none', "opacity":'0.5'});
					$('[data-navs-actions="RemoveSelectAll"]').hide();
					$('[data-navs-actions="SelectAll"]').show();
				}	
			}

			$('body').on("click",'[data-selected-postactions]',function() {
				$(this).closest('.-contain-MiniBox').toggleClass('selected');
				HowToCheckBoxNow( $(this).data('uniqid') );
			});

		// # POST NAV ACTIONS.
			$('body').on("click",'[data-navs-actions]',function(){
				var BTN,Action,Uniq;
				BTN = $(this);
				
				Action = BTN.data('navs-actions');
				Uniq = BTN.data('uniqid');
				var List = [];

				$('.-ScrollerCenter[data-uniqid="'+Uniq+'"] > .-contain-MiniBox'+(( Action == 'RemoveAllSelected' ) ? '.selected' : '')).each(function(e,v){
					if( Action == 'SelectAll' ){
						$(v).addClass('selected');
						$('[data-navs-actions="SelectAll"]').hide();
						$('[data-navs-actions="RemoveSelectAll"]').show();
					}else if( Action == 'RemoveSelectAll' ){
						$(v).removeClass('selected');
						$('[data-navs-actions="SelectAll"]').show();
						$('[data-navs-actions="RemoveSelectAll"]').hide();
					}else if( Action == 'RemoveAllSelected' ){
						List.push( $(v).data('post-id') );				
					}
				});

				if( Action == 'RemoveAllSelected' ){
					var Argums = List.join(',');
				  var Data = {
				    "headtitle":'هل تريد حذف '+List.length+' عنصر ؟ ',
				    "alertcontent" : 'هل تريد بالتأكيد حذف '+List.length+' نموذج ؟',
				    "ConfirmAttrs" : 'data-remove-post-id="'+Argums+'" data-alert="true" data-uniq="'+Uniq+'" data-location="stay"',
				  }
				  RemoveAlert(Data);
				}
				HowToCheckBoxNow(Uniq);
			});

		// # REMOVE POST BY ID.	
			$('body').on("click",'[data-remove-post-id]',function(e){e.preventDefault();
				var BTN,removedID,DataAlert,CommentElement;
				BTN = $(this);
				removedID = BTN.data('remove-post-id')+'';
				DataAlert = BTN.data('alert');

				//
				if(DataAlert != undefined && DataAlert != false ){
					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					var location = BTN.data('location');
					var IDList = [];
					if( removedID.indexOf(',') > -1 ){
						var r = ',';
						var x = $.trim(removedID.match(r).toString());
						var str_split = removedID.split(x);
						for (var i = 0; i < str_split.length; i++) {
							if( str_split[i] != undefined ){
		    				IDList.push($.trim(str_split[i]));
							}
						}
					}else{
						IDList.push( removedID );
					}
					$.ajax({
						url: AjaxURL,
						dataType: 'json',
						type : "POST",
						action:'removepost',
						data: {action:"removepost","removedID": removedID,"location":location},
						success: function(msg) {
							$('.Popver--CoursesAlert').remove();
				      $.each( IDList,function(e,fe){
				      	$('.-contain-MiniBox[data-post-id="'+fe+'"]').remove();
				      });

				      if( msg.reload__page != undefined ){
								setTimeout(function(){
									window.location.href = msg.reload__page;
								}, 300);
				      }

						}
					});
				}else{
				  var Data = {
				    "headtitle":'هل تريد حذف الطلب',
				    "alertcontent" : 'هل تريد بالتأكيد حذف هذا الطلب نهائياً؟',
				    "ConfirmAttrs" : 'data-remove-post-id="'+removedID+'" data-alert="true" data-location="'+( ( BTN.data('location') != undefined ) ? BTN.data('stay') : 'stay')+'"',
				  }
				  RemoveAlert(Data);
				}
			});



});