/**
 * fork of acf-flexible-content-modal by edir pedro
 * https://github.com/edirpedro/acf-flexible-content-modal
 */


(function($){
	
	// acf events
	acf.add_action('ready', acf_fc_modal_init);

	acf.add_action('append', function($el){
		// Ignoring if it is a nested FC
		if ($el.parents('.acf-field-flexible-content').length > 1){
			return false;
		}
		$el.find('> .acf-fc-layout-controlls a.-pencil').on('click', acf_fc_modal_open);
	});

	// Init Modal
	function acf_fc_modal_init() {
		
		$('.lablab-content-area .acf-flexible-content .layout').each(function() {
			
			var layout = $(this);
					
			// Ignoring if it is a nested FC
			if (layout.parents('.acf-field-flexible-content').length > 1){
				return true;
			}
			
			// Remove Toggle button
			layout.find('> .acf-fc-layout-controlls a.-collapse').parent('li').remove(); // ACF 5.4
			layout.find('> .acf-fc-layout-controlls > a.-collapse').remove(); // ACF 5.5

			// Remove acf collapsed classes
			layout.removeClass('-collapsed');

			
			// Edit button
			var controls = layout.find('> .acf-fc-layout-controlls');
			if (controls.is('ul')){
				controls.append('<li><a class="acf-icon -pencil small" href="#" data-event="edit-layout" title="'+lablabModal.editLayout+'"></a></li>');
			}
			else {
				controls.append('<a class="acf-icon -pencil small" href="#" data-event="edit-layout" title="'+lablabModal.editLayout+'"></a>');
			}

			
			// Open Modal
			layout.find('> .acf-fc-layout-controlls a.-pencil').on('click', acf_fc_modal_open);
			
			// Add modal elements
			layout.prepend('<div class="acf-fc-modal-title"></div>');
			layout.find('> .acf-fields, > .acf-table').wrapAll('<div class="acf-fc-modal-content"></div>');
						
		});

	}

	// Open Modal
	function acf_fc_modal_open(e) {
		
		var layout = $(this).closest('.layout');
		if (!layout.hasClass('-modal')) {
			var caption = layout.find('> .acf-fc-layout-handle').clone().find('.lablab-fc-column-width-wrapper').remove().end().html();
			// clone module layout handle to prevent grid layout from being changed (visually) while modal is open
			layout.clone().find('div:not(.acf-fc-layout-handle)').remove().end().addClass('cloned-layout').insertAfter(layout);
			layout.find('.acf-fc-modal-title').html(caption + '<a class="acf-icon -cancel" href="#">');
			layout.addClass('-modal active');
			$("body").append("<div id='TB_overlay'></div>");
			$("#TB_overlay, .acf-fc-modal-title a.-cancel").on('click', layout, acf_fc_modal_remove);
			$('body').addClass('acf-modal-open');
		}
	}

	// Close Modal
	function acf_fc_modal_remove(e) {
		var layout = e.data;
		$('.cloned-layout').remove();
		$("#TB_overlay").remove();
		$('#select2-drop-mask').click(); // forward click event to select2 mask (has lower z-index) to remove select2 dropdown
		layout.removeClass('-modal');
		$('body').removeClass('acf-modal-open');
		setTimeout(function(){
			layout.removeClass('active');
		}, 50);
	}

})(jQuery);