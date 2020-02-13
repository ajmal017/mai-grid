(function($){

	/**
	*  initialize_field
	*
	*  This function will initialize the $field.
	*
	*  @date	30/11/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function initialize_field( $field ) {

		// add sortable
		$field.find( '.acf-sortable-checkbox-list' ).sortable({
			items: '> li',
			handle: '> .sortable-checkbox-handle',
			// forceHelperSize: true,
			placeholder: 'sortable-checkbox-placeholder',
			forcePlaceholderSize: true,
			scroll: true,
			stop: function(event, ui) {
				// self.render();
			},
			update: function(event, ui) {
				// console.log( $(this).prev( 'input[type="hidden"]' ) );
				$(this).prev( 'input[type="hidden"]' ).trigger( 'change' );
			}
		});

	}


	if( typeof acf.add_action !== 'undefined' ) {

		/*
		*  ready & append (ACF5)
		*
		*  These two events are called when a field element is ready for initizliation.
		*  - ready: on page load similar to $(document).ready()
		*  - append: on new DOM elements appended via repeater field or other AJAX calls
		*
		*  @param	n/a
		*  @return	n/a
		*/

		acf.add_action('ready_field/type=sortable_checkbox', initialize_field);
		acf.add_action('append_field/type=sortable_checkbox', initialize_field);


	} else {

		/*
		*  acf/setup_fields (ACF4)
		*
		*  These single event is called when a field element is ready for initizliation.
		*
		*  @param	event		an event object. This can be ignored
		*  @param	element		An element which contains the new HTML
		*  @return	n/a
		*/

		$(document).on('acf/setup_fields', function(e, postbox){

			// find all relevant fields
			$(postbox).find('.field[data-field_type="sortable_checkbox"]').each(function(){

				// initialize
				initialize_field( $(this) );

			});

		});

	}

})(jQuery);
