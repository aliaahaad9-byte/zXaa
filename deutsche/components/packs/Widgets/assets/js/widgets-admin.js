function b64DecodeUnicode(str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent(atob(str).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
}
jQuery(function($){
	$("body").on("click", 'theme-widget-options > h2', function(){
		$(this).parent().find('theme-widget-stack').slideToggle(100);
		$(this).toggleClass('open');
		PinnedJQuery();
		PinnedJQueryV2();
	});
	$("body").on("click", 'theme-widget-action.addmore', function(){
		var nextNumb, nextKey;
		$(this).closest('theme-widget').find('theme-widget-options').each(function(els, el){
			nextNumb = $(el).find('h2 > p > strong').data("i") + 1;
			nextKey = $(el).find('h2 > p > strong').data("key") + 1;
		});
		//
		var $html = b64DecodeUnicode($(this).parent().find('[structure]').html());
		$html = $html.replaceAll("{num}", nextNumb);
		$html = $html.replaceAll("{key}", nextKey);
		$(this).closest('theme-widget-actions').before($html);
		PinnedJQuery();
		PinnedJQueryV2();
	});
	$("body").on("click", 'theme-widget-options > h2 > .remove', function(){
		$(this).closest('.widget-inside').find('[name="savewidget"]').removeAttr("disabled").attr("value", 'حفظ');
		$(this).closest('theme-widget-options').remove();
	});
	function PinnedJQueryV2() {
		$('theme-widget[data-repeat="1"]').each(function(index, el){
			$(el).sortable({
				sort: function( event, ui ) {
					$(el).closest('.widget-inside').find('[name="savewidget"]').removeAttr("disabled").attr("value", 'حفظ');
				}
			});
		});
	}
	PinnedJQueryV2();
});