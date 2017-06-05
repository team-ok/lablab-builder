(function($){

// prevent nesting in already nested layouts
acf.add_action('ready', function(){
	$('.acf-button[data-event="add-layout"]').each(function(){
		var parentLayout = $(this).closest('.layout').data('layout');
		if ( parentLayout ){
			var popup = $(this).closest('.acf-flexible-content').find('script.tmpl-popup');
			var $popupHTML = $( popup.html() );
			$popupHTML.find('a[data-layout="'+parentLayout+'"]').parent().remove();
			popup.html( $popupHTML[0] );
		}
	});
});

})(jQuery);