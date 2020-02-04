<?php

// add_action( 'genesis_before_loop', function() {
// 	vd( is_numeric( '0' ) );
// });

// Get it started.
// add_action( 'plugins_loaded', function() {
// 	new Mai_Grid_Base;
// });

/**
 * This should handle templates, sanitization, enqueing of files, etc., but nothing with ACF
 * since ACF should only be used for the block. We need a shortcode and helper function as well,
 * outside of ACF.
 */
abstract class Mai_Grid_Base {

	protected $type;
	protected $template;
	protected $args;

	protected $loader;
	protected $base_url;
	protected $base_dir;
	protected $version;
	protected $suffix;
	protected $templates;
	protected $fields;
	protected $values;

	// protected $block;

	function __construct( $type, $template, $args ) {

		$this->type      = $type;
		$this->template  = $template;
		$this->args      = $args;

		$this->base_url  = MAI_GRID_PLUGIN_URL . 'assets';
		$this->base_dir  = MAI_GRID_PLUGIN_DIR . 'assets';
		$this->version   = MAI_GRID_VERSION;
		$this->suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		$this->templates = $this->get_templates();
		$this->fields    = $this->get_fields();
		// $this->values    = array();
	}

	function get() {

		$this->values = $this->get_values();
		$this->loader = new Mai_Grid_Template_Loader;

		$grid = $this->get_grid();

		if ( ! $grid ) {
			return;
		}

		$this->enqueue_assets();

		$html = genesis_markup( array(
			'open'    => '<div %s>',
			'context' => 'mai-grid',
			'params'  => $this->values,
			'atts'    => $this->get_attributes(),
			'echo'    => false,
		) );

			$html .= genesis_markup( array(
				'open'    => '<div %s>',
				'context' => 'mai-grid__wrap',
				'params'  => $this->values,
				'echo'    => false,
			) );

				$html .= $grid;

			$html .= genesis_markup( array(
				'close'   => '</div>',
				'context' => 'mai-grid__wrap',
				'echo'    => false,
			) );

		$html .= genesis_markup( array(
			'close'   => '</div>',
			'context' => 'mai-grid',
			'echo'    => false,
		) );

		return $html;
	}

	function get_grid() {
		// switch ( $this->type ) {
		// 	case 'post':
		// 		$grid = new Mai_Post_Grid;
		// 		break;
		// 	case 'category':
		// 		$grid = '';
		// 		break;
		// 	default:
		// 		$grid = '';
		// }
		// return $grid;
	}

	// function do_grid() {
	// 	echo $this->get_grid();
	// }

	/**
	 *
	 */
	function get_templates() {
		$templates = array(
			'standard' => array(
				'label'    => __( 'Standard', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'image_size',
					'image_align',
					'show_title',
					'show_header_meta',
					'header_meta',
					'show_excerpt',
					'show_content',
					'content_limit',
					'show_more_link',
					'more_link_text',
					'show_footer_meta',
					'footer_meta',
					'boxed',
					'align_text',
				),
			),
			'background' => array(
				'label'    => __( 'Background Image', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'image_size',
					'show_title',
					'align_text',
					'align_text_vertical',
				),
			),
			'compact' => array(
				'label'    => __( 'Compact', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'image_align',
					'show_title',
					'boxed',
				),
			),
			// TODO: Horizontal?
		);
		// Filter so custom sites can add their own templates.
		return apply_filters( 'mai_grid_templates', $templates );
	}

	function get_fields() {
		return array_merge(
			$this->get_display_fields(),
			$this->get_layout_fields(),
			$this->get_query_fields(),
		);
	}

	function get_display_fields() {
		return array(
			'template'            => 'field_5de9b96fb69b0',
			'show_image'          => 'field_5e1e665ffc7e5',
			'image_size'          => 'field_5bd50e580d1e9',
			'image_align'         => 'field_5e2f3adf82130',
			'show_title'          => 'field_5e1e6693fc7e6',
			'show_header_meta'    => 'field_5e1e680ce988d',
			'header_meta'         => 'field_5e2b563a7c6cf',
			'show_excerpt'        => 'field_5e1e67e7e988b',
			'show_content'        => 'field_5e1e67fce988c',
			'content_limit'       => 'field_5bd51ac107244',
			'show_more_link'      => 'field_5e1e6843e988f',
			'more_link_text'      => 'field_5c85465018395',
			'show_footer_meta'    => 'field_5e1e6835e988e',
			'footer_meta'         => 'field_5e2b567e7c6d0',
			'boxed'               => 'field_5e2a08a182c2c',
			'align_text'          => 'field_5c853f84eacd6',
			'align_text_vertical' => 'field_5e2f519edc912',
		);
	}

	function get_layout_fields() {
		return array(
			'columns_responsive'     => 'field_5e334124b905d',
			'columns'                => 'field_5c854069d358c',
			'columns_md'             => 'field_5e3305dff9d8b',
			'columns_sm'             => 'field_5e3305f1f9d8c',
			'columns_xs'             => 'field_5e332a5f7fe08',
			'align_columns'          => 'field_5c853e6672972',
			'align_columns_vertical' => 'field_5e31d5f0e2867',
			'column_gap'             => 'field_5c8542d6a67c5',
			'row_gap'                => 'field_5e29f1785bcb6',
		);
	}

	/**
	 *
	 */
	function get_query_fields() {
		return array();
	}

	function enqueue_assets() {

		// Default layout CSS.
		$this->enqueue_asset( 'entries', 'css' );

		if ( is_admin() ) {

			// We can't dynamically load assets via ajax when the template select field changes, so we need them all available in the backend.
			foreach( array_keys( $this->templates ) as $template ) {
				$this->enqueue_asset( $template, 'css' );
			}

			$this->enqueue_asset( 'admin', 'css' );

			// wp_enqueue_script( 'mai-acf-wp-query', "{$this->base_url}/js/mai-acf-wp-query{$this->suffix}.js", array(), $this->version . '.' . date( 'njYHi', filemtime( "{$this->base_dir}/js/mai-acf-wp-query{$this->suffix}.js" ) ), true );
			$this->enqueue_asset( 'wp-query', 'js' );
			// wp_localize_script( 'mai-grid-wp-query', 'maiGridWPQueryVars', array(
			// 	'fields' => $this->get_wp_query_fields(),
			// 	'keys'   => array_values( $this->get_query_fields() ),
			// ) );

		} else {

			// Only load the template CSS file we need.
			$this->enqueue_asset( $this->template, 'css' );
		}
	}

	/**
	 * Enqueue an asset.
	 *
	 * @param   string  $name  The asset name.
	 * @param   string  $type  The type. Typically js or css.
	 *
	 * @return  void
	 */
	function enqueue_asset( $name, $type ) {
		if ( ! file_exists( "{$this->base_dir}/{$type}/{$name}{$this->suffix}.{$type}" ) ) {
			return;
		}
		$url     = sprintf( '%s/%s/%s%s.%s', $this->base_url, $type, $name, $this->suffix, $type );
		$version = $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/{$type}/{$name}{$this->suffix}.{$type}" ) );
		switch ( $type ) {
			case 'css':
				wp_enqueue_style( "mai-grid-{$name}", $url, array(), $version );
				break;
			case 'js':
				wp_enqueue_script( "mai-grid-{$name}", $url, array(), $version, true );
				break;
		}
	}

	/**
	 * Get escaped field from ACF.
	 *
	 * @param   string  $field_key
	 * @param   string  $function    The function to use for escaping.
	 * @param   bool    $allow_null  Wether to return or escape if the value is.
	 *
	 * @return  mixed
	 */
	function get_field( $field_key, $function = 'esc_html', $allow_null = false ) {

		// Get the field, via ACF.
		$data = get_field( $field_key );

		// Return null if allowing null.
		if ( is_null( $data ) && $allow_null ) {
			return $data;
		}

		// If array, escape and return it.
		if ( is_array( $data ) ) {
			$escaped = array();
			foreach( $data as $index => $value ) {
				$value = trim( $value );
				$escaped[ $index ] = $function( $value );
			}
			return $escaped;
		}

		// Return single value.
		$value   = trim( $data );
		$escaped = $function( $value );
		return $escaped;
	}

	function get_values() {
		return array_merge( $this->get_query_values(), $this->get_base_values() );
	}

	function get_query_values() {
		return array();
	}

	function get_base_values() {

		// TODO: Finish correct sanitization and typecasting.

		// Display.
		$values = array(
			'show_image'             => $this->get_field( 'show_image', 'esc_html' ),
			'show_title'             => $this->get_field( 'show_title', 'esc_html' ),
			'show_header_meta'       => $this->get_field( 'show_header_meta', 'esc_html' ),
			'show_excerpt'           => $this->get_field( 'show_excerpt', 'esc_html' ),
			'show_content'           => $this->get_field( 'show_content', 'esc_html' ),
			'show_more_link'         => $this->get_field( 'show_more_link', 'esc_html' ),
			'show_footer_meta'       => $this->get_field( 'show_footer_meta', 'esc_html' ),
			'boxed'                  => $this->get_field( 'boxed', 'esc_html' ),

			'content_limit'          => $this->get_field( 'content_limit', 'esc_html' ),
			'image_size'             => $this->get_field( 'image_size', 'esc_html' ),
			'image_align'            => $this->get_field( 'image_align', 'esc_html' ),
			'more_link_text'         => $this->get_field( 'more_link_text', 'esc_html' ),
			'header_meta'            => $this->get_field( 'header_meta', 'esc_html' ),
			'footer_meta'            => $this->get_field( 'footer_meta', 'esc_html' ),
			// Layout.
			'template'               => $this->get_field( 'template', 'esc_html' ),
			'columns_responsive'     => $this->get_field( 'columns_responsive', 'esc_html' ),
			'columns'                => $this->get_field( 'columns', 'esc_html' ),
			'columns_md'             => $this->get_field( 'columns_md', 'esc_html' ),
			'columns_sm'             => $this->get_field( 'columns_sm', 'esc_html' ),
			'columns_xs'             => $this->get_field( 'columns_xs', 'esc_html' ),
			// 'column_min_width'       => $this->get_field( 'column_min_width', 'esc_html' ),
			'align_columns'          => $this->get_field( 'align_columns', 'esc_html' ),
			'align_columns_vertical' => $this->get_field( 'align_columns_vertical', 'esc_html' ),
			'align_text'             => $this->get_field( 'align_text', 'esc_html' ),
			'align_text_vertical'    => $this->get_field( 'align_text_vertical', 'esc_html' ),
			'column_gap'             => $this->get_field( 'column_gap', 'esc_html' ),
			'row_gap'                => $this->get_field( 'row_gap', 'esc_html' ),
		);

		// This should probably be a filter, maybe right in $data before passing to templates.
		$values['more_link_text'] = $values['more_link_text'] ?: __( 'Read More', 'mai-grid' );

		return $values;
	}

	function get_attributes() {
		// Start the attributes.
		$attributes = array(
			'class' => sprintf( 'mai-grid mai-grid-%s', sanitize_html_class( $this->values['template'] ) ),
			'style' => '',
		);
		// Global styles.
		$attributes['style'] .= sprintf( '--columns:%s;', $this->values['columns'] );
		$attributes['style'] .= sprintf( '--columns-md:%s;', $this->get_responsive_columns( $this->values['columns_md'], $this->values['columns'], $this->values['columns'] ) );
		$attributes['style'] .= sprintf( '--columns-sm:%s;', $this->get_responsive_columns( $this->values['columns_sm'], $this->values['columns_md'], $this->values['columns'] ) );
		$attributes['style'] .= sprintf( '--columns-xs:%s;', $this->get_responsive_columns( $this->values['columns_xs'], $this->values['columns_sm'], $this->values['columns'] ) );
		$attributes['style'] .= sprintf( '--column-gap:%s;', $this->values['column_gap'] );
		$attributes['style'] .= sprintf( '--row-gap:%s;', $this->values['row_gap'] );
		$attributes['style'] .= sprintf( '--align-columns:%s;', $this->values['align_columns'] );
		$attributes['style'] .= sprintf( '--align-columns-vertical:%s;', $this->values['align_columns_vertical'] );
		// Template based classes.
		if ( $this->template_supports( $this->values['template'], 'boxed' ) && $this->values['boxed'] ) {
			$attributes['class'] .= ' has-boxed';
		}
		if ( $this->template_supports( $this->values['template'], 'show_image' ) && $this->template_supports( $this->values['template'], 'image_align' ) && $this->values['show_image'] && $this->values['image_align'] ) {
			$attributes['class'] .= ' has-image-align-' . $this->values['image_align'];
		}
		// Template based styles.
		if ( $this->template_supports( $this->values['template'], 'align_text' ) ) {
			$attributes['style'] .= sprintf( '--align-text:%s;', $this->values['align_text'] );
		}
		if ( $this->template_supports( $this->values['template'], 'align_text_vertical' ) ) {
			$attributes['style'] .= sprintf( '--align-text-vertical:%s;', $this->values['align_text_vertical'] );
		}
		if ( $this->template_supports( $this->values['template'], 'align_text_vertical' ) ) {
			$attributes['style'] .= sprintf( '--aspect-ratio:%s;', $this->values['show_image'] ? $this->get_aspect_ratio( $this->values['image_size'] ) : '4/3' );
		}
		// Send it.
		return $attributes;
	}

	/**
	 *
	 * TODO: Will this break if more than one grid on a page?
	 */
	function get_responsive_columns( $value, $previous_value, $original_value ) {
		static $current_columns;
		// Set the current column.
		if ( ! isset( $current_columns ) ) {
			$current_columns = $original_value;
		}
		// If using responsive settings, and have a value.
		if ( $this->values['columns_responsive'] && is_numeric( $value ) ) {
			$current_columns = $value;
			return $current_columns;
		}
		$compare = is_numeric( $previous_value ) ? $previous_value : $current_columns;
		switch ( $compare ) {
			case 6:
				$current_columns = 4;
			break;
			case 5:
				$current_columns = 3;
			break;
			case 4:
				$current_columns = 2;
			break;
			case 3:
				$current_columns = 2;
			break;
			case 2:
				$current_columns = 1;
			break;
			case 1:
				$current_columns = 1;
			break;
			case 0:
				$current_columns = 0;
			break;
		}
		return absint( $current_columns );
	}

	function get_aspect_ratio( $image_size ) {
		$sizes = $this->get_image_sizes( $image_size );
		return sprintf( '%s/%s', $sizes[0], $sizes[1] );
	}

	/**
	 * Get an image width and height.
	 *
	 * @return  array  An array with [0] being width and [1] being height.
	 */
	function get_image_sizes( $image_size ) {
		global $_wp_additional_image_sizes;
		// Get width/height from global image sizes.
		if ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {
			$registered_image = $_wp_additional_image_sizes[ $image_size ];
			$width  = $registered_image['width'];
			$height = $registered_image['height'];
		}
		// Fallback.
		else {
			$width  = 4;
			$height = 3;
		}
		return array( $width, $height );
	}

	function get_flex_align( $value ) {
		switch ( $value ) {
			case 'start':
			case 'top':
				$return = 'flex-start';
				break;
			case 'center':
			case 'middle':
				$return = 'center';
				break;
			case 'right':
			case 'bottom':
				$return = 'flex-end';
				break;
			default:
				$return = 'unset';
		}
		return $return;
	}

	/**
	 * Get the gap value.
	 * If only a number value, force to pixels.
	 */
	function get_gap( $value ) {
		if ( empty( $value ) || is_numeric( $value ) ) {
			return sprintf( '%spx', intval( $value ) );
		}
		return trim( $value );
	}

	function template_supports( $template, $value ) {
		return isset( $this->templates[ $template ]['supports'] ) && in_array( $value, $this->templates[ $template ]['supports'] );
	}
}
