jQuery(function($){
	$('body').on('click', '.YTSUploadButton', function(e){
		var field = $(this).data('field');
		var name = $(this).data('name');
		var datamultiple = $(this).data('multiple');
		e.preventDefault();
     		var button = $(this),
    		    custom_uploader = wp.media({
			title: $(this).data('rlname'),
			library : {
				type : $(this).data('type')
			},
			button: {
				text: UploadButtonText
			},
			multiple: $(this).data('multiple')
		}).on('select', function() {
			if( datamultiple == true ) {
				var attachments = custom_uploader.state().get('selection'),
				    attachment_ids = new Array(),
				    i = 0;
				attachments.each(function(attachment) {
	 				attachment_ids[i] = attachment['id'];
	 				var attachment = attachment.toJSON();
					$(field+'_preview').append('<span><input type="hidden" name="'+name+'['+attachment.id+']" value="'+attachment.url+'" /><em onClick="this.parent().remove();"><span></span><span></span></em><img src="'+attachment.url+'" /></span>');
					i++;
				});
			}else {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$(field+'_id').val(attachment.id);
				$(field).val(attachment.url);
				$(field+'_preview').attr('src', attachment.url).show();
				$(field+'_remove').show();
			}
		})
		.open();
	});
 
	$('body').on('click', '.YTSRemoveButton', function(){
		if( $(this).data('multiple') == false ) {
			$(this).prev().attr('src', '').hide();
			$(this).prev().prev().prev().val('');
			$(this).prev().prev().prev().prev().val('');
			$(this).remove();
		}else {
			$(this).prev().html('');
			$(this).remove();
		}
		return false;
	});
});