<?php

/**
 * This should handle templates, sanitization, enqueing of files, etc., but nothing with ACF
 * since ACF should only be used for the block. We need a shortcode and helper function as well,
 * outside of ACF.
 */
class Mai_Grid_Base {

	protected $fields;
	protected $args;
	protected $version;

	function __construct( $args ) {

		$config          = new Mai_Settings_Config;
		$this->fields    = $config->get_fields();
		$this->keys      = $config->get_keys();
		$this->args      = $this->get_args( $args );
		$this->version   = MAI_GRID_VERSION;
	}

	function get_args( $args ) {

		// Parse args.
		$args = shortcode_atts( [
			'type'                   => 'post',  // post, term, user.
			'context'                => 'block', // block, singular, archive.
			'class'                  => '',
			// Display.
			'show'                   => $this->fields['show']['default'],
			'image_orientation'      => $this->fields['image_orientation']['default'],
			'image_size'             => $this->fields['image_size']['default'],
			'image_position'         => $this->fields['image_position']['default'],
			'header_meta'            => $this->fields['header_meta']['default'],
			'content_limit'          => $this->fields['content_limit']['default'],
			'more_link_text'         => $this->fields['more_link_text']['default'],
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
			// 'taxonomy'               => $this->fields['taxonomies']['acf']['sub_fields']['taxonomy']['default'],
			// 'terms'                  => $this->fields['taxonomies']['acf']['sub_fields']['terms']['default'],
			// 'operator'               => $this->fields['taxonomies']['acf']['sub_fields']['operator']['default'],
			'taxonomies_relation'    => $this->fields['taxonomies_relation']['default'],
			'post_parent__in'        => $this->fields['post_parent__in']['default'],
			'orderby'                => $this->fields['orderby']['default'],
			'orderby_meta_key'       => $this->fields['orderby_meta_key']['default'],
			'order'                  => $this->fields['order']['default'],
			'exclude'                => $this->fields['exclude']['default'],
		], $args, 'mai_grid' );

		// Sanitize.
		$args = [
			'type'                   => $this->sanitize( $args['type'], 'esc_html' ),
			'context'                => $this->sanitize( $args['context'], 'esc_html' ),
			'class'                  => $this->sanitize( $args['class'], 'esc_html' ),
			// Display.
			'show'                   => $this->sanitize( $args['show'], 'esc_html' ),
			'image_orientation'      => $this->sanitize( $args['image_orientation'], 'esc_html' ),
			'image_size'             => $this->sanitize( $args['image_size'], 'esc_html' ),
			'image_position'         => $this->sanitize( $args['image_position'], 'esc_html' ),
			'header_meta'            => $this->sanitize( $args['header_meta'], 'esc_html' ),
			'content_limit'          => $this->sanitize( $args['content_limit'], 'esc_html' ),
			'more_link_text'         => $this->sanitize( $args['more_link_text'], 'esc_html' ),
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
			'number'                 => $this->sanitize( $args['number'], 'absint' ),
			'offset'                 => $this->sanitize( $args['offset'], 'absint' ),
			'query_by'               => $this->sanitize( $args['query_by'], 'esc_html' ),
			'post__in'               => (array) $this->sanitize( $args['post__in'], 'absint' ),
			'post__not_in'           => (array) $this->sanitize( $args['post__not_in'], 'absint' ),
			'taxonomies'             => $this->sanitize( $args['taxonomies'], 'esc_html' ),
			// 'taxonomy'               => $this->sanitize( $args['taxonomy'], 'esc_html' ),
			// 'terms'                  => $this->sanitize( $args['terms'], 'esc_html' ),
			// 'operator'               => $this->sanitize( $args['operator'], 'esc_html' ),
			'taxonomies_relation'    => $this->sanitize( $args['taxonomies_relation'], 'esc_html' ),
			'post_parent__in'        => $this->sanitize( $args['post_parent__in'], 'absint' ),
			'orderby'                => $this->sanitize( $args['orderby'], 'esc_html' ),
			'orderby_meta_key'       => $this->sanitize( $args['orderby_meta_key'], 'esc_html' ),
			'order'                  => $this->sanitize( $args['order'], 'esc_html' ),
			'exclude'                => (array) $this->sanitize( $args['exclude'], 'esc_html' ),
		];

		return apply_filters( 'mai_grid_args', $args );
	}

	function render() {

		// Bail if not showing any elements.
		if ( empty( $this->args['show'] ) ) {
			return;
		}

		// Enqueue scripts and styles.
		$this->enqueue_assets();

		// Grid specific classes.
		$this->args['class'] = 'mai-grid ' . $this->args['class'];
		$this->args['class'] = trim( $this->args['class'] );

		// Open.
		mai_do_entries_open( $this->args );

			// Entries.
			$this->do_grid_entries();

		// Close.
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

	function get_post_query_args() {

		$query_args = [
			'post_type'           => $this->args['post_type'],
			'posts_per_page'      => $this->args['number'],
			'post_status'         => 'publish',
			'offset'              => absint( $this->args['offset'] ),
			'ignore_sticky_posts' => true,
		];

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
				$tax_query = [];
				foreach( $this->args['taxonomies'] as $taxo ) {
					// Skip if we don't have all the tax query args.
					if ( ! ( $taxo['taxonomy'] && $taxo['taxonomy'] && $taxo['taxonomy'] ) ) {
						continue;
					}
					// Set the value.
					$tax_query[] = [
						'taxonomy' => $taxo['taxonomy'],
						'field'    => 'id',
						'terms'    => $taxo['terms'],
						'operator' => $taxo['operator'],
					];
				}
				// If we have tax query values.
				if ( $tax_query ) {
					$query_args['tax_query'] = $tax_query;
					if ( $this->args['taxonomies_relation'] ) {
						$query_args['tax_query'][] = [
							'relation' => $this->args['taxonomies_relation'],
						];
					}
				}
			break;
		}

		// Exclude entries.
		if ( ( 'title' !== $this->args['query_by'] ) && $this->args['post__not_in'] ) {
			$query_args['post__not_in'] = $this->args['post__not_in'];
		}

		// Orderby.
		if ( $this->args['orderby'] ) {
			$query_args['orderby'] = $this->args['orderby'];
			if ( 'meta_value_num' === $this->args['orderby'] ) {
				$query_args['meta_key'] = $this->args['orderby_meta_key'];
			}
		}

		// Order.
		if ( $this->args['order'] ) {
			$query_args['order'] = $this->args['order'];
		}

		return apply_filters( 'mai_post_grid_query_args', $query_args );
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
			$escaped = [];
			foreach( $value as $index => $item ) {
				if ( is_array( $item ) ) {
					$escaped[ $index ] = $this->sanitize( $item, $function );
				} else {
					$item = trim( $item );
					$escaped[ $index ] = $function( $item );
				}
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
					// $fields = $keys = [];
					// foreach( $this->fields as $name => $field ) {
					// 	if ( ! $field['block'] ) {
					// 		continue;
					// 	}
					// 	$fields[ $name ] = $field['key'];
					// 	$keys[]          = $field['key'];
					// 	// Add sub_fields.
					// 	if ( isset( $field['acf']['sub_fields'] ) ) {
					// 		foreach( $field['acf']['sub_fields'] as $sub_name => $sub_field ) {
					// 			$fields[ $sub_name ] = $sub_field['key'];
					// 			$keys[]              = $sub_field['key'];
					// 		}
					// 	}
					// }
					// vdd( $this->keys );
					wp_localize_script( 'mai-grid-wp-query', 'maiGridWPQueryVars', [
						'fields' => $this->fields,
						'keys'   => $this->keys,
					] );
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
