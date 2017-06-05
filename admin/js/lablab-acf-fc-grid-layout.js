(function($){

	var options = {}; // gets populated with select option values on acf ready
	var acfVersion;


	/**
	 * Init
	 */

	acf.add_action('ready', function(){

		acfVersion = parseFloat(acf.o.acf_version);

		// select only closest .values (not nested ones)
		var $contentAreas = $('.lablab-content-area').find('.acf-row:not(.acf-clone)').find('.lablab-content-elements > .acf-input > .acf-flexible-content > .values');
		var $modules = $contentAreas.find('> .layout');

		// flexible content area as grid layout
		$contentAreas.addClass('uk-grid').attr('data-uk-grid-margin', '');

		// store column width options values (fractions and corresponding floats)
		// lablabColumnWidthOptions = global object set by wp_localize_script function
		$.each(lablabColumnWidthOptions.options, function(key, label){
			options[key] = getFractionAsFloat(key);
		});

		// add buttons to make a column fill up the rows free space
		$modules.each(function(index, module){
			addFillUpButton( $(module) );
		});

		$contentAreas.each(function(index, contentArea){
			refreshFillUpButtons( $(contentArea) );
		});

	});


	/**
	 * Actions 'Ready' and 'Append' on select fields
	 */

	acf.add_action('ready_field/type=select append_field/type=select', function($el){ // $el = div.acf-field-select (jQuery object)

		if ($el.data('name') == 'lablab-column-width'){

			if ( acf.select2.version >= 4 ){
				$select = $el.find('.acf-input > select');
			} else {
				$select = $el.find('.acf-input > input');
			}
			
			// bind event handler
			$select.on('change', function(){

				// add width classes to columns
				setColumnWidth( $(this) );

				// enable/disable fill up buttons
				refreshFillUpButtons( $(this).closest('.values') );

				// refresh width label via acf ajax method (returned html filtered by lablab_fc_grid_module_title_html (php) )
				acf.fields.flexible_content.render_layout_title( $(this).closest('.layout') );

			});

			// add width classes to columns
			setColumnWidth($select);

			// highlight select2 options 
			$select.on('select2:open select2-open', function(){
				var $module = $(this).closest('.layout');
				// wait until select2 dropdown is ready
				setTimeout(function(){
					highlightOptions($module);
				}, 50);
			});
		}

	});



	/**
	 * Action 'Remove'
	 */

	acf.add_action('remove', function($el){ // $el = div.layout (jQuery object) or tr.acf-row (jQuery object)

		// bail early if action was triggered outside of lablab builder
		if ( $el.closest('.acf-field-repeater').data('name') != 'lablab-content-area' ){
			return false;
		}
		
		// acf bug?: remove event gets triggered when appending a fc layout (module) or a repeater row (content area)
		// remove event gets triggered when adding images (even when selecting images in the media library)
		// https://support.advancedcustomfields.com/forums/topic/acf-action-remove-fires-on-add-row/
		// therefore: check if it's a real remove event and if it was triggered when removing a module
		// check if it wasn't triggered inside of a nested FC
		if ( ! $el.hasClass('layout') || $el.prevObject.hasClass( "acf-clone" ) || $el.parents('.acf-field-flexible-content').length > 1 ){
			return false;
		}

		// wait until element is removed
		setTimeout(function(){
			refreshFillUpButtons( $el.closest('.values') );
		}, 50);

	});

	/**
	 * Action 'Append'
	 */

	acf.add_action('append', function($el){ // $el = div.layout (jQuery object) or tr.acf-row (jQuery object)

		// bail early if action was triggered outside of lablab builder
		if ( $el.closest('.acf-field-repeater').data('name') != 'lablab-content-area' ){
			return false;
		}

		// when a new content area was added
		if ( $el.hasClass('acf-row') ){

			// flexible content area as grid layout
			$el.find('.values').addClass('uk-grid').attr('data-uk-grid-margin', '');

		// when a new module was added
		} else {
			if ( $el.closest('.acf-field-flexible-content').data('name') == 'lablab-content-elements' ){

				addFillUpButton($el);
				refreshFillUpButtons( $el.closest('.values') );
				// refresh width label via acf ajax method (returned html filtered by lablab_fc_grid_module_title_html)
				acf.fields.flexible_content.render_layout_title($el);
			}
		}

	});


	/**
	 * Action 'Sortstop'
	 */

	acf.add_action('sortstop', function($el){ // $el = div.layout (jQuery object) or tr.acf-row (jQuery object)

		// bail early if action was triggered outside of lablab builder or if the element that was moved is an acf repeater row
		if ( $el.closest('.acf-field-repeater').data('name') != 'lablab-content-area' || $el.hasClass('.acf-row') ){
			return false;
		}
		refreshFillUpButtons( $el.closest('.values') );

	});


	/**
	 * Change width of a column
	 */

	function setColumnWidth($select){ // $select = select (jQuery object)

		var columnWidth = $select.val();

		$select.closest('.layout')
		// remove previous uk-width-class
		.removeClass(function(index, css) {
			return (css.match(/\buk-width-\S+/g) || []).join(' ')
		})
		.addClass('uk-width-medium-' + columnWidth);
	}


	/**
	 * Create/refresh object representation of a content area (order and width of columns and free space in a row)
	 */

	function refreshGridObject($grid){ // $grid = .values (jQuery object)

		// get all layouts (modules) of the current flexible content area
		var $modules = $grid.find('> .layout');
		var columnWidthSum = 0;
		var rows = [];
		var freeSpace = 0;
		var rowIndex = 0;
		var colIndex = 0;

		$modules.find('[data-name="lablab-column-width"] > .acf-input > select').each(function(){

			// calculate width of column from input value string
			var fractionString = ( acf.select2.version >= 4 ? $(this).val() : $(this).siblings('input').val() );
			var thisColumnWidth = 0;
			if ( (/^\d[-]\d+$/).test(fractionString) ){ // check if value is of format "number hyphen number")
				thisColumnWidth = getFractionAsFloat( fractionString );
			} else {
				console.log('Column-width value has to be of format "number hyphen number".');
			}
			
			// add width of column to sum
			columnWidthSum += thisColumnWidth;

			// if the sum of the column's widths is greater than 100%, start new row
			if ( columnWidthSum > 1 ){
				columnWidthSum = thisColumnWidth;
				rowIndex++;
				colIndex = 0;
			}

			// calculate free space in row
			freeSpace = Math.abs(1 - columnWidthSum);

			// maybe create empty row object
			rows[rowIndex] = rows[rowIndex] || {};
			
			// free space value gets overwritten until a new row index is set (when columnWidthSum is greater than 1)
			rows[rowIndex]['freeSpace'] = round2(freeSpace);

			// maybe create empty column array
			rows[rowIndex]['columns'] = rows[rowIndex]['columns'] || [];

			// push column objects into array
			rows[rowIndex]['columns'].push({
				width: round2(thisColumnWidth),
				columnWidthSum: round2(columnWidthSum)
			});

			// store grid indices
			$(this).closest('.layout').data('lablabRow', rowIndex).data('lablabColumn', colIndex);
			// increment column index
			colIndex++;
		});

		return rows;
	}


	/**
	 * Add fill-up buttons to each column
	 */

	function addFillUpButton($module){ // $module = div.layout (jQuery object)
		
		var $fillUpButton = $('<a class="acf-icon -right small" href="#" title="Fill up row" data-event="fill-row"></a>')
		.on('click', function(){
			var $thisModule = $(this).closest('.layout');
			var fillUp = $thisModule.data('fillUp'); // get stored fillUp value
			var $columnWidthInput = $thisModule.find('.acf-fields:first > div[data-name="lablab-column-width"] > .acf-input');
			// change select2 hidden input value to fillUp
			if ( acf.select2.version >= 4 ){
				$columnWidthInput.find('> select').val(fillUp).change();
			} else {
				$columnWidthInput.find('> input').val(fillUp).change();
				// refresh select2 chosen label
				$columnWidthInput.find('.select2-chosen').text(lablabColumnWidthOptions.options[fillUp]);
			}
		});
		if ( acfVersion < 5.5 ){
			$fillUpButton.insertAfter( $module.find('> .acf-fc-layout-controlls > li > a[data-event="remove-layout"]').parent() ).wrap('<li></li>');
		} else {
			$fillUpButton.insertAfter( $module.find('> .acf-fc-layout-controlls > a[data-event="remove-layout"]') );
		}
	}


	/**
	 * Enable/disable fill-up buttons dynamically
	 */

	function refreshFillUpButtons($grid){ // $grid = div.values (jQuery object)

		var rows = refreshGridObject($grid);

		$grid.find('> .layout:not(.cloned-layout)').each(function(index, module){

			var $module = $(module);
			var rowIndex = $module.data('lablabRow');
			var colIndex = $module.data('lablabColumn');
			var freeSpace = rows[rowIndex] && rows[rowIndex].freeSpace;
			var column = rows[rowIndex] && rows[rowIndex].columns[colIndex];
			var active = false;
			var $fillUpButton = $module.find('.acf-fc-layout-controlls > a[data-event="fill-row"]');
			$fillUpButton = ( $fillUpButton.length > 0 ? $fillUpButton : $module.find('.acf-fc-layout-controlls > li > a[data-event="fill-row"]') );

			if ( freeSpace > 0 ){
		 	
		 		// step through column width options
		 		$.each(options, function(optFraction, optFloat) {
		 			
		 			if ( prettyEqual(optFloat, freeSpace + column.width ) ){
		 				active = true;
		 				$module.data('fillUp', optFraction );

		 				// exit when values match
		 				return false;
		 			}

		 		});

		 		$module.removeClass('lablab-full-row');

			} else {
				// add full-row class for highlighting
				$module.addClass('lablab-full-row');
			}
			// set or remove active class
			if ( active ){
				$fillUpButton.addClass('active');
			} else {
				$fillUpButton.removeClass('active');
			}
		});
	}


	/**
	 * Dynamically highlight select2 options
	 */

	function highlightOptions($module){ // $module = div.layout (jQuery object)

		var rows = refreshGridObject( $module.closest('.values') );
		var rowIndex = $module.data('lablabRow');
		var colIndex = $module.data('lablabColumn');
		var thisColumn = rows[rowIndex].columns[colIndex];
		var freeSpace = rows[rowIndex].freeSpace;
		var prevFreeSpace = (rowIndex > 0 ? rows[rowIndex - 1].freeSpace : 0);
		var i = 0;
		var select2Options = ( acf.select2.version >= 4 ? $('.select2-results__option') : $('.select2-result') );

		// find width values that will make the current column 
		// fit in or fill up the current row or the previous one
			 	
	 	// step through column width options
	 	$.each(options, function(optFraction, optFloat) {
	 		
	 		var optionClass;
	 		var optionNote;

 			// match the value that is pretty equal to the current width
 			if ( prettyEqual(optFloat, thisColumn.width ) ){

 				optionClass = 'lablab-no-change';
 				optionNote = lablabColumnWidthOptions.text.currentWidth;

 			// match the width value that will fill up the row
 			} else if ( freeSpace > 0 && prettyEqual(optFloat, freeSpace + thisColumn.width) ){
 				
 				optionClass = 'lablab-fill-row';
 				optionNote = lablabColumnWidthOptions.text.fillRow;

 			// match the width values that will fit in the row
 			} else if ( optFloat < round2(freeSpace + thisColumn.width) ){
 				
 				optionClass = 'lablab-fit-in-row';
 				optionNote = lablabColumnWidthOptions.text.fitRow;

 			// match the values that will move the current column to a new row
 			} else if ( optFloat > round2( 1 - thisColumn.columnWidthSum + thisColumn.width ) ) {
 				
 				optionClass = 'lablab-self-to-next-row';
 				optionNote = lablabColumnWidthOptions.text.selfToNextRow;
 			
 			// selecting any other option will move following elements to a new row
 			} else {

 				optionClass = 'lablab-following-to-next-row';
 				optionNote = lablabColumnWidthOptions.text.followingToNextRow;	
 			}
	 		
	 		// only first column in a row (except first row), where the previous row has some free space left
	 		if (colIndex == 0 && prevFreeSpace > 0){

		 		// match the value that will fill up the previous row
	 			if ( prettyEqual(prevFreeSpace, optFloat) ){

	 				// highlight the corresponding select2 option
	 				optionClass = 'lablab-fill-prev-row';
		 			optionNote = lablabColumnWidthOptions.text.fillPrevRow;

		 		// match the value that will move the column to the previous row
	 			} else if ( prevFreeSpace > optFloat ){

	 				// highlight the corresponding select2 option
	 				optionClass = 'lablab-fit-in-prev-row';
		 			optionNote = lablabColumnWidthOptions.text.selfToPrevRow;
	 			}

	 		}

 			// highlight the corresponding select2 option
	 		// search-functionality must be disabled, otherwise index matching won't work properly
			if (optionClass && optionNote){
				select2Options.eq(i++)
				.addClass(optionClass)
				.append('<small class="lablab-note">'+optionNote+'</small>');
			}

	 	});

	}

	/**
	 * Select2 Args
	 */

	acf.add_filter('select2_args', function( args, $select, settings, $field ){

		$field = $field || $select.closest('.acf-field-select'); // $field is not set in older versions of acf 

		if ( $field.data('name') == 'lablab-column-width'){
			
			// no search box
			args = $.extend({
				minimumResultsForSearch: -1,
			}, args);

		}

		return args;
			
	});

	/**
	 * Helpers
	 */

	function getFractionAsFloat(fractionString){

		// split values of type "1-4" into array and divide array[0] by array[1]
		return fractionString.split('-').reduce(divide);
	}

	function divide(numerator, denominator){

		return numerator / denominator;
	}

	function round2(number){

		return Math.round( number * 100 ) / 100;
	}

	function prettyEqual(a,b){

		return round2( Math.abs(a - b) ) <= 0.01;
	}

})(jQuery);