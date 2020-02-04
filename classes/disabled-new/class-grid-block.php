<?php

class Mai_Grid_Block extends Mai_Grid_Base {

	protected $filters_run;

	function __construct() {

		// Run field group filters once.
		$this->filters_run = false;
		if ( is_admin() && ! $this->filters_run ) {
			$this->run_block_filters();
			$this->filters_run = true;
		}
	}

	function run_block_filters() {

		// Add field wrapper classes.
		add_filter( 'acf/field_wrapper_attributes', function( $wrapper, $field ) {
			// Show.
			if ( in_array( $field['key'], array(
				$this->fields['show_image'],
				$this->fields['show_title'],
				$this->fields['show_header_meta'],
				$this->fields['show_excerpt'],
				$this->fields['show_content'],
				$this->fields['show_more_link'],
				$this->fields['show_footer_meta'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-show';
			}
			// Conditional Show.
			if ( in_array( $field['key'], array(
				$this->fields['image_size'],
				$this->fields['image_align'],
				$this->fields['header_meta'],
				$this->fields['content_limit'],
				$this->fields['more_link_text'],
				$this->fields['footer_meta'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-show-conditional';
			}
			// Button Group.
			if ( in_array( $field['key'], array(
				$this->fields['align_text'],
				$this->fields['align_text_vertical'],
				$this->fields['columns'],
				$this->fields['columns_md'],
				$this->fields['columns_sm'],
				$this->fields['columns_xs'],
				$this->fields['align_columns'],
				$this->fields['align_columns_vertical'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-button-group';
			}
			// Button Group.
			if ( in_array( $field['key'], array(
				$this->fields['align_text'],
				$this->fields['align_text_vertical'],
				$this->fields['columns_md'],
				$this->fields['columns_sm'],
				$this->fields['columns_xs'],
				$this->fields['align_columns'],
				$this->fields['align_columns_vertical'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-button-group-clear';
			}
			// Nested Columns.
			if ( in_array( $field['key'], array(
				$this->fields['columns_md'],
				$this->fields['columns_sm'],
				$this->fields['columns_xs'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-nested-columns';
			}
			// Nested Columns First.
			if ( in_array( $field['key'], array(
				$this->fields['columns_md'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-nested-columns-first';
			}
			// Nested Columns Last.
			if ( in_array( $field['key'], array(
				$this->fields['columns_xs'],
			) ) ) {
				$wrapper['class'] .= ' mai-grid-nested-columns-last';
			}
			return $wrapper;
		}, 10, 2 );

		/**
		 * Display.
		 */
		// Template.
		add_filter( "acf/load_field/key={$this->fields['template']}",               array( $this, 'load_templates' ) );
		// Image Size.
		add_filter( "acf/load_field/key={$this->fields['image_size']}",             array( $this, 'load_image_sizes' ) );
		// Image Alignment.
		add_filter( "acf/load_field/key={$this->fields['image_align']}",            array( $this, 'load_image_align' ) );
		// More Link Text.
		add_filter( "acf/load_field/key={$this->fields['more_link_text']}",         array( $this, 'load_more_link_text' ) );

		/**
		 * Layout.
		 */
		// Columns.
		add_filter( "acf/load_field/key={$this->fields['columns']}",                array( $this, 'load_columns' ) );
		add_filter( "acf/load_field/key={$this->fields['columns_md']}",             array( $this, 'load_columns_responsive' ) );
		add_filter( "acf/load_field/key={$this->fields['columns_sm']}",             array( $this, 'load_columns_responsive' ) );
		add_filter( "acf/load_field/key={$this->fields['columns_xs']}",             array( $this, 'load_columns_responsive' ) );
		// Align Columns.
		add_filter( "acf/load_field/key={$this->fields['align_columns']}",          array( $this, 'load_align_columns' ) );
		add_filter( "acf/load_field/key={$this->fields['align_columns_vertical']}", array( $this, 'load_align_columns_vertical' ) );
		// Align Text.
		add_filter( "acf/load_field/key={$this->fields['align_text']}",             array( $this, 'load_align_text' ) );
		add_filter( "acf/load_field/key={$this->fields['align_text_vertical']}",    array( $this, 'load_align_text_vertical' ) );

		// Labels.
		foreach( $this->get_display_fields() as $name => $key ) {
			// Skip if name does not contain 'show_'.
			if ( false === strpos ( $name, 'show_' ) ) {
				continue;
			}
			add_filter( "acf/load_field/key={$key}", function( $field ) {
				// Keep admin clean.
				if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
					return $field;
				}
				// Clear label.
				$field['label'] = '';
				// TODO: JS to get template value and set defaults? Too aggressive?
				// $field['default'] = '';
				return $field;
			});
		}
		// Conditionals.
		foreach( $this->get_display_fields() as $name => $key ) {
			// Skip template field.
			if ( 'template' === $name ) {
				continue;
			}
			// Add filter.
			add_filter( "acf/load_field/key={$key}", function( $field ) {
				// Keep admin clean.
				if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
					return $field;
				}
				$field = $this->add_conditional_logic( $field );
				return $field;
			});
		}
	}

	function load_templates( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		if ( ! $this->templates ) {
			return $field;
		}

		foreach( $this->templates as $name => $template ) {
			$field['choices'][ $name ] = $template['label'];
		}

		// TODO: Filter via config or something?!?

		$field['default_value'] = 'standard';

		return $field;
	}

	/**
	 * Load image sizes.
	 * Much of the code take from genesis_get_image_sizes().
	 */
	function load_image_sizes( $field ) {

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// $field['choices'] = array();
			$field['choices'] = array( 'default' => __( 'Default' ) );
			return $field;
		}

		global $_wp_additional_image_sizes;
		$sizes = $field['choices'] = array( 'default' => __( 'Default' ) );
		foreach ( get_intermediate_image_sizes() as $size ) {
			if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$sizes[ $size ] = array(
					'width'  => absint( $_wp_additional_image_sizes[ $size ]['width'] ),
					'height' => absint( $_wp_additional_image_sizes[ $size ]['height'] ),
					'crop'   => $_wp_additional_image_sizes[ $size ]['crop'],
				);
			} else {
				$sizes[ $size ] = array(
					'width'  => absint( get_option( "{$size}_size_w" ) ),
					'height' => absint( get_option( "{$size}_size_h" ) ),
					'crop'   => (bool) get_option( "{$size}_crop" ),
				);
			}
		}
		foreach ( $sizes as $index => $value ) {
			if ( 'default' === $index ) {
				$field['choices'][$index] = $index;
			} else {
				$field['choices'][$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
			}
		}

		// Let's use a default and let code decide the default size based on the Template.
		$field['default_value'] = 'default';

		// $field['default_value'] = isset( $sizes['one-third'] ) ? 'one-third' : 'thumbnail';
		return $field;
	}

	function load_image_align( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			''       => __( 'Default', 'mai-grid' ),
			'left'   => __( 'Left', 'mai-grid' ),
			'center' => __( 'Center', 'mai-grid' ),
			'right'  => __( 'Right', 'mai-grid' ),
		);

		return $field;
	}

	function load_align_columns( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			''       => __( 'Clear', 'mai-grid' ),
			'left'   => __( 'Left', 'mai-grid' ),
			'center' => __( 'Center', 'mai-grid' ),
			'right'  => __( 'Right', 'mai-grid' ),
		);

		$field['default'] = '';

		return $field;
	}

	function load_align_columns_vertical( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			''       => __( 'Clear', 'mai-grid' ),
			'top'    => __( 'Top', 'mai-grid' ),
			'middle' => __( 'Middle', 'mai-grid' ),
			'bottom' => __( 'Bottom', 'mai-grid' ),
		);

		$field['default'] = '';

		return $field;
	}

	function load_align_text( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			''       => __( 'Clear', 'mai-grid' ),
			'left'   => __( 'Left', 'mai-grid' ),
			'center' => __( 'Center', 'mai-grid' ),
			'right'  => __( 'Right', 'mai-grid' ),
		);

		$field['default'] = '';

		return $field;
	}

	function load_align_text_vertical( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			''       => __( 'Clear', 'mai-grid' ),
			'top'    => __( 'Top', 'mai-grid' ),
			'middle' => __( 'Middle', 'mai-grid' ),
			'bottom' => __( 'Bottom', 'mai-grid' ),
		);

		$field['default'] = '';

		return $field;
	}

	function load_columns( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			1 => __( '1', 'mai-grid' ),
			2 => __( '2', 'mai-grid' ),
			3 => __( '3', 'mai-grid' ),
			4 => __( '4', 'mai-grid' ),
			5 => __( '5', 'mai-grid' ),
			6 => __( '6', 'mai-grid' ),
			0 => __( 'Auto', 'mai-grid' ),
		);

		$field['default_value'] = 3;

		return $field;
	}


	function load_columns_responsive( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'' => __( 'Clear', 'mai-grid' ),
			1  => __( '1', 'mai-grid' ),
			2  => __( '2', 'mai-grid' ),
			3  => __( '3', 'mai-grid' ),
			4  => __( '4', 'mai-grid' ),
			5  => __( '5', 'mai-grid' ),
			6  => __( '6', 'mai-grid' ),
			0  => __( 'Auto', 'mai-grid' ),
		);

		$field['default_value'] = '';

		return $field;
	}

	function load_more_link_text( $field ) {
		$field['placeholder'] = __( 'Read More', 'mai-grid' );
		return $field;
	}

	function add_conditional_logic( $field ) {

		// Build conditions.
		$conditions = array();
		foreach( $this->templates as $template_name => $template_values ) {
			// Skip if field is supported.
			if ( in_array( $field['name'], $template_values['supports'] ) ) {
				continue;
			}
			// Add condition to hide field.
			$conditions[] = array(
				'field'    => $this->fields['template'],
				'operator' => '!=',
				'value'    => $template_name,
			);
		}

		// Bail if no new conditions on this field.
		if ( empty( $conditions ) ) {
			return $field;
		}

		// If existing conditional logic.
		if ( $field['conditional_logic'] ) {
			// Loop through and add this condition to each.
			foreach( $field['conditional_logic'] as $logic_index => $logic_values ) {
				// $field['conditional_logic'][ $logic_index ][] = $conditions;
				$field['conditional_logic'][ $logic_index ] = array_merge( $logic_values, $conditions );
			}
		}
		// No existing conditions.
		else {
			$field['conditional_logic'] = array( $conditions );
		}

		// Send it.
		return $field;
	}

}
