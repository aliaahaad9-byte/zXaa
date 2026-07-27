var AllElements = '#APBMetaBox-IMDbOptions, #APBMetaBox-SeriesExtractor, #tagsdiv-interest, #productiondiv, #mpaadiv, .apb-field-imdbRating, .apb-field-imdb_votes, #APBMetaBox-PostOptions, #APBMetaBox-WatchServers, #APBMetaBox-DownloadServers, #tagsdiv-production, #APBMetaBox-SettingOptions, #APBMetaBox-ActorNames, .apb-field-number, #seriesdiv, #tagsdiv-movseries, #tagsdiv-post_tag, #tagsdiv-release-year, #tagsdiv-genre, #tagsdiv-Quality, #tagsdiv-nation, #tagsdiv-language, #tagsdiv-actor, #tagsdiv-director, #tagsdiv-writers, #tagsdiv-awards';
var SeriesElements = '#APBMetaBox-WatchServers, #APBMetaBox-DownloadServers, #APBMetaBox-SettingOptions, .apb-field-number, #seriesdiv, #tagsdiv-post_tag, #tagsdiv-Quality';
var MovieElements = '#APBMetaBox-IMDbOptions, #APBMetaBox-SeriesExtractor, #tagsdiv-interest, #productiondiv, #mpaadiv, .apb-field-imdbRating, .apb-field-imdb_votes, #APBMetaBox-PostOptions, #APBMetaBox-WatchServers, #APBMetaBox-DownloadServers, #tagsdiv-production, #APBMetaBox-SettingOptions, #APBMetaBox-ActorNames, #tagsdiv-post_tag, #tagsdiv-release-year, #tagsdiv-genre, #tagsdiv-Quality, #tagsdiv-nation, #tagsdiv-language, #tagsdiv-actor, #tagsdiv-director, #tagsdiv-writers, #tagsdiv-awards';
var MovserieElements = '#APBMetaBox-IMDbOptions, #APBMetaBox-SeriesExtractor, #tagsdiv-interest, #productiondiv, #mpaadiv, .apb-field-imdbRating, .apb-field-imdb_votes, #APBMetaBox-PostOptions, #APBMetaBox-WatchServers, #APBMetaBox-DownloadServers, #tagsdiv-production, #APBMetaBox-SettingOptions, #APBMetaBox-ActorNames, #tagsdiv-movseries, #tagsdiv-post_tag, #tagsdiv-release-year, #tagsdiv-genre, #tagsdiv-Quality, #tagsdiv-nation, #tagsdiv-language, #tagsdiv-actor, #tagsdiv-director, #tagsdiv-writers, #tagsdiv-awards';
var $ = jQuery;
function SwitchType(el) {
	$('.AddPostSwitchParent > ul > li').removeClass('active');
	$(el).addClass('active');
	$(AllElements).hide();
	if( $(el).data('type') == 'movie' ) {
		$(MovieElements).show();
	}else if( $(el).data('type') == 'movseries' ) {
		$(MovserieElements).show();
	}else {
		$(SeriesElements).show();
	}
}
$(AllElements).hide();
if( $('.AddPostSwitchParent > ul > li.active').data('type') == 'movie' ) {
	$(MovieElements).show();
}else if( $('.AddPostSwitchParent > ul > li.active').data('type') == 'movseries' ) {
	$(MovserieElements).show();
}else if( $('.AddPostSwitchParent > ul > li.active').data('type') == 'episode' ){
	$(SeriesElements).show();
}
$( ".apbsortable" ).sortable();
$( ".apbsortable" ).disableSelection();
//////////////////////////////////
$('[class*="apb-field-SectionQuery_"]').hide();
$('[id*="APBMetaBox-SectionQuery_"]').hide();
$('body').on('change', '#SectionQuery', function(){
	$('[class*="apb-field-SectionQuery_"]').hide();
	$('[id*="APBMetaBox-SectionQuery_"]').hide();
	if( $(this).val() == 'posts' ) {
		$('[class*="apb-field-SectionQuery_'+$(this).val()+'"]').show();
	}else {
		$('[id*="APBMetaBox-SectionQuery_'+$(this).val()+'"]').show();
	}
});
var el = $('#SectionQuery');
if( el.val() == 'posts' ) {
	$('[class*="apb-field-SectionQuery_"]').hide();
	$('[class*="apb-field-SectionQuery_'+el.val()+'"]').show();
}else {
	$('[class*="apb-field-SectionQuery_"]').hide();
	$('[id*="APBMetaBox-SectionQuery_"]').hide();
	$('[id*="APBMetaBox-SectionQuery_'+el.val()+'"]').show();
}