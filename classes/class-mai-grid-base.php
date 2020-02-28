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
		$config        = new Mai_Settings_Config( 'block' );
		$this->fields  = $config->get_fields();
		$this->keys    = $config->get_keys();
		$this->args    = $this->get_args( $args );
		$this->version = MAI_GRID_VERSION;
	}

	function get_args( $args ) {

		// Bail if no type & context.
		if ( ! isset( $args['type'], $args['context'] ) ) {
			return;
		}

		/**
		 * Get defaults and parse args.
		 * Skip tabs.
		 * Skip if not the type/context we need.
		 * Check for sub fields first.
		 */
		$defaults = [
			'type'    => 'post',  // post, term, user.
			'context' => 'block', // block, singular, archive.
			'class'   => '',
		];
		foreach( $this->fields as $name => $field ) {
			// Skip if not the context we want.
			if ( ! ( isset( $field[ $args['context'] ] ) && $field[ $args['context'] ] ) ) {
				continue;
			}
			// Skip if field type is a tab.
			if ( 'tab' === $field['type'] ) {
				continue;
			}
			// Skip if block and not the block we need.
			if ( 'block' === $args['context'] ) {
				/**
				 * Skip if not in the grid we need.
				 * Nested conditionals so we don't do in_array() for no reason if not a block.
				 */
				if ( ! in_array( sprintf( 'mai_%s_grid', $args['type'] ), $field['group'] ) ) {
					continue;
				}
			}
			// Add to our defaults.
			$defaults[ $name ] = $field;
		}

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Sanitize.
		foreach( $args as $name => $value ) {
			// Has sub fields.
			if ( isset( $this->fields[ $name ]['acf']['sub_fields'] ) ) {
				if ( $value ) {
					$sub_values = [];
					foreach( $value as $index => $group ) {
						foreach( $group as $sub_name => $sub_value ) {
							$field = $this->fields[ $name ]['acf']['sub_fields'][ $sub_name ];
							$sub_values[ $index ][ $sub_name ] = $this->sanitize( $sub_value, $field['sanitize'] );
						}
					}
					$args[ $name ] = $sub_values;
				}
			}
			// Standard field.
			else {
				// Get the sanitization function, as type/context aren't actual fields.
				$sanitize = isset( $this->fields[ $name ] ) ? $this->fields[ $name ]['sanitize'] : 'esc_html';
				$args[ $name ] = $this->sanitize( $value, $sanitize );
			}
		}

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
			case 'tax_meta':
				$tax_query = $meta_query = [];
				if ( $this->args['taxonomies'] ) {
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
				}
				// TODO: Add meta_query.
				if ( $this->args['meta_keys'] ) {

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
