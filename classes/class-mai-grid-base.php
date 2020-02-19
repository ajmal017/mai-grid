<?php

// add_action( 'genesis_before_loop', function() {
// 	vd( is_numeric( '0' ) );
// });

// Get it started.
// add_action( 'plugins_loaded', function() {
// 	new Mai_Grid_Base;
// });

// Image_Processing_Queue\Queue::instance();
// $attempts = apply_filters( 'ipq_job_attempts', 3 );
// $interval = apply_filters( 'ipq_cron_interval', 1 );
// wp_queue()->cron( $attempts, $interval );

// // Make sure we have the database tables we need.
// add_action( 'admin_init', function() {

// 	$tables = get_option( 'mai_ipq_tables_installed', 0 );

// 	if ( ! $tables ) {
// 		wp_queue_install_tables();
// 		update_site_option( 'mai_ipq_tables_installed', '1' );
// 	}
// });

/**
 * This should handle templates, sanitization, enqueing of files, etc., but nothing with ACF
 * since ACF should only be used for the block. We need a shortcode and helper function as well,
 * outside of ACF.
 */
class Mai_Grid_Base {

	// protected $type;
	protected $args;
	// protected $fields;

	// protected $base_url;
	// protected $base_dir;
	protected $version;
	// protected $suffix;
	// protected $templates;
	protected $defaults;
	protected $fields;
	// protected $values;

	function __construct( $args ) {

		// $this->args['type']      = $type;

		$this->args      = $this->get_args( $args );

		// $this->base_url  = MAI_GRID_PLUGIN_URL . 'assets';
		// $this->base_dir  = MAI_GRID_PLUGIN_DIR . 'assets';
		$this->version   = MAI_GRID_VERSION;
		// $this->suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		// $this->templates = self::get_templates();
		$this->defaults  = self::get_defaults();
		$this->fields    = self::get_fields();
	}

	function get_args( $args ) {

		// Parse args.
		$args = shortcode_atts( array(
			'type'                   => 'post',  // post, term, user.
			'context'                => 'block', // block, singular, archive.
			'class'                  => '',
			// Display.
			// 'template'               => $this->fields['template']['default'],
			'show'                   => $this->fields['show']['default'],
			// // 'show_image'             => $this->fields['show_image']['default'],
			'image_orientation'      => $this->fields['image_orientation']['default'],
			'image_size'             => $this->fields['image_size']['default'],
			'image_position'            => $this->fields['image_position']['default'],
			// // 'show_title'             => $this->fields['show_title']['default'],
			// // 'show_header_meta'       => $this->fields['show_header_meta']['default'],
			'header_meta'            => $this->fields['header_meta']['default'],
			// // 'show_excerpt'           => $this->fields['show_excerpt']['default'],
			// // 'show_content'           => $this->fields['show_content']['default'],
			'content_limit'          => $this->fields['content_limit']['default'],
			// // 'show_more_link'         => $this->fields['show_more_link']['default'],
			'more_link_text'         => $this->fields['more_link_text']['default'],
			// // 'show_footer_meta'       => $this->fields['show_footer_meta']['default'],
			'footer_meta'            => $this->fields['footer_meta']['default'],
			'boxed'                  => $this->fields['boxed']['default'],
			'align_text'             => $this->fields['align_text']['default'],
			'align_text_vertical'    => $this->fields['align_text_vertical']['default'],
			// Layout.
			'columns_responsive'     => $this->fields['columns_responsive']['default'],
			'columns'                => $this->fields['columns']['default'],
			'columns_md'             => $this->fields['columns_md']['default'],
			'columns_sm'             => $this->fields['columns_sm']['default'],
			'columns_xs'             => $this->fields['columns_xs']['default'],
			'align_columns'          => $this->fields['align_columns']['default'],
			'align_columns_vertical' => $this->fields['align_columns_vertical']['default'],
			'column_gap'             => $this->fields['column_gap']['default'],
			'row_gap'                => $this->fields['row_gap']['default'],
			// WP_Query.
			'post_type'              => $this->fields['post_type']['default'],
			'number'                 => $this->fields['number']['default'],
			'offset'                 => $this->fields['offset']['default'],
			'query_by'               => $this->fields['query_by']['default'],
			'post__in'               => $this->fields['post__in']['default'],
			'post__not_in'           => $this->fields['post__not_in']['default'],
			'taxonomies'             => $this->fields['taxonomies']['default'],
			'taxonomy'               => $this->fields['taxonomy']['default'],
			'terms'                  => $this->fields['terms']['default'],
			'relation'               => $this->fields['relation']['default'],
			'operator'               => $this->fields['operator']['default'],
			'relation'               => $this->fields['relation']['default'],
			'post_parent__in'        => $this->fields['post_parent__in']['default'],
			'orderby'                => $this->fields['orderby']['default'],
			'meta_key'               => $this->fields['meta_key']['default'],
			'order'                  => $this->fields['order']['default'],
			'exclude'                => $this->fields['exclude']['default'],
		), $args, 'mai_grid' );

		// Sanitize.
		$args = array(
			'type'                   => $this->sanitize( $args['type'], 'esc_html' ),
			'context'                => $this->sanitize( $args['context'], 'esc_html' ),
			'class'                  => $this->sanitize( $args['class'], 'esc_html' ),
			// Display.
			// 'template'               => $this->sanitize( $args['template'], 'esc_html' ),
			'show'                   => $this->sanitize( $args['show'], 'esc_html' ),
			// // 'show_image'             => $this->sanitize( $args['show_image'], 'esc_html' ),
			'image_orientation'      => $this->sanitize( $args['image_orientation'], 'esc_html' ),
			'image_size'             => $this->sanitize( $args['image_size'], 'esc_html' ),
			'image_position'            => $this->sanitize( $args['image_position'], 'esc_html' ),
			// // 'show_title'             => $this->sanitize( $args['show_title'], 'esc_html' ),
			// // 'show_header_meta'       => $this->sanitize( $args['show_header_meta'], 'esc_html' ),
			'header_meta'            => $this->sanitize( $args['header_meta'], 'esc_html' ),
			// // 'show_excerpt'           => $this->sanitize( $args['show_excerpt'], 'esc_html' ),
			// // 'show_content'           => $this->sanitize( $args['show_content'], 'esc_html' ),
			'content_limit'          => $this->sanitize( $args['content_limit'], 'esc_html' ),
			// // 'show_more_link'         => $this->sanitize( $args['show_more_link'], 'esc_html' ),
			'more_link_text'         => $this->sanitize( $args['more_link_text'], 'esc_html' ),
			// 'show_footer_meta'       => $this->sanitize( $args['show_footer_meta'], 'esc_html' ),
			'footer_meta'            => $this->sanitize( $args['footer_meta'], 'esc_html' ),
			'boxed'                  => $this->sanitize( $args['boxed'], 'esc_html' ),
			'align_text'             => $this->sanitize( $args['align_text'], 'esc_html' ),
			'align_text_vertical'    => $this->sanitize( $args['align_text_vertical'], 'esc_html' ),
			// Layout.
			'columns_responsive'     => $this->sanitize( $args['columns_responsive'], 'esc_html' ),
			'columns'                => $this->sanitize( $args['columns'], 'esc_html' ),
			'columns_md'             => $this->sanitize( $args['columns_md'], 'esc_html' ),
			'columns_sm'             => $this->sanitize( $args['columns_sm'], 'esc_html' ),
			'columns_xs'             => $this->sanitize( $args['columns_xs'], 'esc_html' ),
			'align_columns'          => $this->sanitize( $args['align_columns'], 'esc_html' ),
			'align_columns_vertical' => $this->sanitize( $args['align_columns_vertical'], 'esc_html' ),
			'column_gap'             => $this->sanitize( $args['column_gap'], 'esc_html' ),
			'row_gap'                => $this->sanitize( $args['row_gap'], 'esc_html' ),
			// WP_Query.
			'post_type'              => (array) $this->sanitize( $args['post_type'], 'esc_html' ),
			'number'                 => $this->sanitize( $args['number'], 'esc_html' ),
			'offset'                 => $this->sanitize( $args['offset'], 'esc_html' ),
			'query_by'               => $this->sanitize( $args['query_by'], 'esc_html' ),
			'post__in'               => (array) $this->sanitize( $args['post__in'], 'esc_html' ),
			'post__not_in'           => (array) $this->sanitize( $args['post__not_in'], 'esc_html' ),
			'taxonomies'             => $this->sanitize( $args['taxonomies'], 'esc_html' ),
			'taxonomy'               => $this->sanitize( $args['taxonomy'], 'esc_html' ),
			'terms'                  => $this->sanitize( $args['terms'], 'esc_html' ),
			'relation'               => $this->sanitize( $args['relation'], 'esc_html' ),
			'operator'               => $this->sanitize( $args['operator'], 'esc_html' ),
			'relation'               => $this->sanitize( $args['relation'], 'esc_html' ),
			'post_parent__in'        => $this->sanitize( $args['post_parent__in'], 'esc_html' ),
			'orderby'                => $this->sanitize( $args['orderby'], 'esc_html' ),
			'meta_key'               => $this->sanitize( $args['meta_key'], 'esc_html' ),
			'order'                  => $this->sanitize( $args['order'], 'esc_html' ),
			'exclude'                => (array) $this->sanitize( $args['exclude'], 'esc_html' ),
		);

		return apply_filters( 'mai_grid_args', $args );
	}

	function render() {

		// Enqueue scripts and styles.
		$this->enqueue_assets();

		$this->args['class'] = 'mai-grid ' . $this->args['class'];
		$this->args['class'] = trim( $this->args['class'] );
		mai_do_entries_open( $this->args );

			$this->do_grid_entries();

		mai_do_entries_close( $this->args );

	}

	function do_grid_entries() {

		switch ( $this->args['type'] ) {
			case 'post':
				$show  = array_flip( $this->args['show'] );
				$posts = new WP_Query( $this->get_post_query_args() );
				if ( $posts->have_posts() ) {
					while ( $posts->have_posts() ) : $posts->the_post();

						global $post;
						mai_do_entry( $post, $this->args );

					endwhile;
				} else {
					// TODO.
				}
				wp_reset_postdata();
			break;
			break;
			case 'term':
				// TODO.
			break;
			case 'user':
				// TODO.
			break;
		}

	}

	function get_grid_entry( $elements, $args = [] ) {

		$elements = wp_parse_args( $elements, [
			'image'       => '',
			'title'       => '',
			'header_meta' => '',
			'excerpt'     => '',
			'content'     => '',
			'more_link'   => '',
			'footer_meta' => '',
		]);

		$args = wp_parse_args( $args, [
			'content' => 'post', // post, term, user,
		]);

		$elements = apply_filters( 'mai_post_grid_entry_elements', $elements, $this->args );

		$html = '';

		// Open.
		// TODO: genesis_markup for attributes, etc.
		$html .= '<article class="mai-grid-entry">';

			// Maybe show image and wrap.
			if ( isset( $show['image'] ) && ( 'full' !== $this->args['image_position'] ) ) {
				// $html .= $elements['image'];
				$html .= '<div class="mai-grid-inner">';
			}

				// Loop through elements.
				foreach ( $this->args['show'] as $element ) {

						do_action( sprintf( 'mai_%s_title', $this->args['type'] ), $this->args );

						// do_action( 'mai_term_title' );
						// do_action( 'mai_term_title' );



					// if ( isset( $show['image'] ) && ( 'image' === $element ) && ( 'full' !== $this->args['image_position'] ) ) {
					// 	continue;
					// }
					// vd( $elements[ $element ] );
					$html .= $elements[ $element ];
				}

			// Maybe close wrap.
			if ( isset( $show['image'] ) && ( 'full' !== $this->args['image_position'] ) ) {
				$html .= '</div>';
			}

		// Close.
		$html .= '</article>';

		return $html;
	}

	static function get_defaults() {
		$fields   = self::get_fields();
		$defaults = [
			'type'    => 'post',
			'context' => 'block',
		];
		foreach( $fields as $key => $field ) {
			$defaults[ $key ] = $field['default'];
		}
		return apply_filters( 'mai_grid_defaults', $defaults );
	}

	static function get_fields() {
		$fields = array_merge(
			self::get_display_fields(),
			self::get_layout_fields(),
			self::get_wp_query_fields(),
			self::get_wp_term_query_fields(),
		);
		return apply_filters( 'mai_grid_fields', $fields );
	}

	static function get_display_fields() {
		return array(
			'show' => array(
				'default' => array( 'title', 'image' ),
				'key'     => 'field_5e441d93d6236',
			),
			'image_orientation' => array(
				'default' => 'landscape',
				'key'     => 'field_5e4d4efe99279',
			),
			'image_size' => array(
				'default' => 'default',
				'key'     => 'field_5bd50e580d1e9',
			),
			'image_position' => array(
				'default' => '',
				'key'     => 'field_5e2f3adf82130',
			),
			'header_meta' => array(
				'default' => '',
				'key'     => 'field_5e2b563a7c6cf',
			),
			'content_limit' => array(
				'default' => '',
				'key'     => 'field_5bd51ac107244',
			),
			'more_link_text' => array(
				// TODO: Filter on this default? Will we have a separate filter in v2?
				'default' => __( 'Read More', 'mai-grid' ),
				'key'     => 'field_5c85465018395',
			),
			'footer_meta' => array(
				'default' => '',
				'key'     => 'field_5e2b567e7c6d0',
			),
			'boxed' => array(
				'default' => '',
				'key'     => 'field_5e2a08a182c2c',
			),
			'align_text' => array(
				'default' => '',
				'key'     => 'field_5c853f84eacd6',
			),
			'align_text_vertical' => array(
				'default' => '',
				'key'     => 'field_5e2f519edc912',
			),
		);
	}

	static function get_layout_fields() {
		return array(
			'columns_responsive' => array(
				'default' => '',
				'key'     => 'field_5e334124b905d',
			),
			'columns' => array(
				'default' => 3,
				'key'     => 'field_5c854069d358c',
			),
			'columns_md' => array(
				'default' => '',
				'key'     => 'field_5e3305dff9d8b',
			),
			'columns_sm' => array(
				'default' => '',
				'key'     => 'field_5e3305f1f9d8c',
			),
			'columns_xs' => array(
				'default' => '',
				'key'     => 'field_5e332a5f7fe08',
			),
			'align_columns' => array(
				'default' => '',
				'key'     => 'field_5c853e6672972',
			),
			'align_columns_vertical' => array(
				'default' => '',
				'key'     => 'field_5e31d5f0e2867',
			),
			'column_gap' => array(
				'default' => '24px',
				'key'     => 'field_5c8542d6a67c5',
			),
			'row_gap' => array(
				'default' => '24px',
				'key'     => 'field_5e29f1785bcb6',
			),
		);
	}

	static function get_wp_query_fields() {
		return array(
			'post_type' => array(
				'default' => array( 'post' ),
				'key'     => 'field_5df1053632ca2',
			),
			'number' => array(
				'default' => '12',
				'key'     => 'field_5df1053632ca8',
			),
			'offset' => array(
				'default' => '',
				'key'     => 'field_5df1bf01ea1de',
			),
			'query_by' => array(
				'default' => 'date',
				'key'     => 'field_5df1053632cad',
			),
			'post__in' => array(
				'default' => '',
				'key'     => 'field_5df1053632cbc',
			),
			'post__not_in' => array(
				'default' => '',
				'key'     => 'field_5e349237e1c01',
			),
			'taxonomies' => array(
				'default' => '',
				'key'     => 'field_5df1397316270',
			),
			'taxonomy' => array(
				'default' => '',
				'key'     => 'field_5df1398916271',
			),
			'terms' => array(
				'default' => '',
				'key'     => 'field_5df139a216272',
			),
			'relation' => array(
				'default' => '',
				'key'     => 'field_5df139281626f',
			),
			'operator' => array(
				'default' => 'IN',
				'key'     => 'field_5df18f2305c2c',
			),
			'relation' => array(
				'default' => '',
				'key'     => 'field_5df139281626f',
			),
			'post_parent__in' => array(
				'default' => '',
				'key'     => 'field_5df1053632ce4',
			),
			'orderby' => array(
				'default' => 'date',
				'key'     => 'field_5df1053632cec',
			),
			'meta_key' => array(
				'default' => '',
				'key'     => 'field_5df1053632cf4',
			),
			'order' => array(
				'default' => '',
				'key'     => 'field_5df1053632cfb',
			),
			'exclude' => array(
				'default' => '',
				'key'     => 'field_5df1053632d03',
			),
		);
	}

	static function get_wp_term_query_fields() {
		return array(

		);
	}

	function get_post_query_args() {

		$query_args = array(
			'post_type'           => $this->args['post_type'],
			'posts_per_page'      => $this->args['number'],
			'post_status'         => 'publish',
			'offset'              => $this->args['offset'],
			'ignore_sticky_posts' => true,
		);

		// Handle query_by.
		switch ( $this->args['query_by'] ) {
			case 'parent':
				$query_args['post_parent__in'] = $this->args['post_parent__in'];
			break;
			case 'title':
				// Empty array returns all posts, so we need to check for values.
				if ( $this->args['post__in'] ) {
					$query_args['post__in'] = $this->args['post__in'];
				}
			break;
			case 'taxonomy':
				$query_args['tax_query'] = array(
					'relation' => $this->args['relation'],
				);
				foreach( $this->args['taxonomies'] as $taxo ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxo['taxonomy'],
						'field'    => 'id',
						'terms'    => $taxo['terms'],
						'operator' => $taxo['operator'],
					);
				}
			break;
		}

		// Exclude entries.
		if ( ( 'title' !== $this->args['query_by'] ) && $this->args['post__not_in'] ) {
			$query_args['post__not_in'] = $this->args['post__not_in'];
		}

		// vd( $args );

		return apply_filters( 'mai_post_grid_query_args', $query_args );
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

	/**
	 * Sanitize a value. Checks for null/array.
	 *
	 * @param   string  $value       The value to sanitize.
	 * @param   string  $function    The function to use for escaping.
	 * @param   bool    $allow_null  Wether to return or escape if the value is.
	 *
	 * @return  mixed
	 */
	function sanitize( $value, $function = 'esc_html', $allow_null = false ) {

		// Return null if allowing null.
		if ( is_null( $value ) && $allow_null ) {
			return $value;
		}

		// If array, escape and return it.
		if ( is_array( $value ) ) {
			$escaped = array();
			foreach( $value as $index => $item ) {
				$item = trim( $item );
				$escaped[ $index ] = $function( $item );
			}
			return $escaped;
		}

		// Return single value.
		$value   = trim( $value );
		$escaped = $function( $value );
		return $escaped;
	}

	function enqueue_assets() {

		// Default layout CSS.
		$this->enqueue_asset( 'entries', 'css' );

		if ( is_admin() ) {

			// Default admin scripts.
			$this->enqueue_asset( 'admin', 'css' );
			// $this->enqueue_asset( 'admin', 'js' );

			// Query JS.
			switch ( $this->args['type'] ) {
				case 'post':
					$this->enqueue_asset( 'wp-query', 'js' );
				break;
				case 'term':
				break;
			}
		}
	}

	/**
	 * Enqueue an asset.
	 *
	 * @param   string  $name          The asset name.
	 * @param   string  $type          The type. Typically js or css.
	 * @param   array   $dependencies  Script dependencies.
	 *
	 * @return  void
	 */
	function enqueue_asset( $name, $type, $dependencies = [] ) {
		// TODO: These should get cleaned up once in the engine.
		$base_url = trailingslashit( MAI_GRID_PLUGIN_URL ) . 'assets/' . $type;
		$base_dir = trailingslashit( MAI_GRID_PLUGIN_DIR ) . 'assets/' . $type;
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		if ( ! file_exists( "{$base_dir}/{$name}{$suffix}.{$type}" ) ) {
			// Fallback if someone overrides the CSS/JS in a theme and doesn't proved .min version.
			if ( '.min' === $suffix ) {
				if ( file_exists( "{$base_dir}/{$name}.{$type}" ) ) {
					$suffix = '';
				} else {
					return;
				}
			}
		}
		$url     = sprintf( '%s/%s%s.%s', $base_url, $name, $suffix, $type );
		$version = $this->version . '.' . date ( 'njYHi', filemtime( "{$base_dir}/{$name}{$suffix}.{$type}" ) );
		switch ( $type ) {
			case 'css':
				wp_enqueue_style( "mai-grid-{$name}", $url, $dependencies, $version );
			break;
			case 'js':
				wp_enqueue_script( "mai-grid-{$name}", $url, $dependencies, $version, true );
			break;
		}
	}

}
