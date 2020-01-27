<?php

// Get it started.
add_action( 'plugins_loaded', function() {
	new Mai_Grid_Block;
});

class Mai_Grid_Block {

	protected $base_url;
	protected $base_dir;
	protected $version;
	protected $suffix;
	protected $fields;
	protected $values;
	protected $block;

	function __construct() {

		$this->base_url = MAI_GRID_PLUGIN_URL . 'assets';
		$this->base_dir = MAI_GRID_PLUGIN_DIR . 'assets';
		$this->version  = MAI_GRID_VERSION;
		$this->suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		$this->fields   = $this->get_fields();
		$this->values   = array();

		add_action( 'acf/init', array( $this, 'register_block' ), 10, 3 );
		$this->filters();
	}

	function register_block() {
		// Bail if no ACF Pro >= 5.8.
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}
		// Register.
		acf_register_block_type( array(
			'name'            => 'mai-grid',
			'title'           => __( 'Mai Grid', 'mai-grid' ),
			'description'     => __( 'Display entries in various layouts..', 'mai-grid' ),
			'icon'            => 'grid-view',
			'category'        => 'widgets',
			'keywords'        => array( 'grid', 'post', 'entries' ),
			// 'mode'            => 'auto',
			// 'mode'            => 'edit',
			'mode'            => 'preview',
			'enqueue_assets'  => array( $this, 'enqueue_assets'),
			'render_callback' => array( $this, 'do_grid_entries' ),
			'supports'        => array(
				'align'  => array( 'wide' ),
				'ancher' => true,
			),
		) );
	}

	function enqueue_assets( $block ) {

		if ( is_admin() ) {

			// We can't dynamically load assets via ajax so we need them all available in the backend.
			foreach( array_keys( $this->get_templates() ) as $template ) {
				$this->enqueue_asset( $template, 'css' );
			}

			$this->enqueue_asset( 'admin', 'css' );

			// wp_enqueue_style( 'mai-grid-admin', "{$this->base_url}/css/admin{$this->suffix}.css", array(), $this->version . '.' . date( 'njYHi', filemtime( "{$this->base_dir}/css/mai-grid-admin{$this->suffix}.css" ) ) );

			// $this->enqueue_asset( 'admin', 'js' );

			// wp_enqueue_script( 'mai-acf-wp-query', "{$this->base_url}/js/mai-acf-wp-query{$this->suffix}.js", array(), $this->version . '.' . date( 'njYHi', filemtime( "{$this->base_dir}/js/mai-acf-wp-query{$this->suffix}.js" ) ), true );
			$this->enqueue_asset( 'wp-query', 'js' );
			wp_localize_script( 'mai-grid-wp-query', 'maiGridWPQueryVars', array(
				'fields' => $this->get_wp_query_fields(),
				'keys'   => array_values( $this->get_wp_query_fields() ),
			) );

			// wp_enqueue_script( 'mai-grid-entries-admin', "{$this->base_url}/js/mai-grid-admin{$this->suffix}.js", array(), $this->version . '.' . date( 'njYHi', filemtime( "{$this->base_dir}/js/mai-grid-admin{$this->suffix}.js" ) ), true );
			// wp_localize_script( 'mai-grid-entries-admin', 'maiGridVars', $this->get_templates() );
		} else {

			$this->enqueue_asset( $block['data']['template'], 'css' );
		}

		// wp_enqueue_style( 'mai-grid', "{$this->base_url}/css/mai-grid-entries{$this->suffix}.css", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/css/mai-grid-entries{$this->suffix}.css" ) ) );
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

	function do_grid_entries( $block, $content = '', $is_preview = false ) {

		$this->block = $block;

		// Get the values.
		$this->values = array(
			// Query.
			'post_type'              => get_field( 'post_type' ),
			'posts_per_page'         => get_field( 'posts_per_page' ),
			'offset'                 => get_field( 'offset' ),
			'query_by'               => get_field( 'query_by' ),
			'post__in'               => get_field( 'post__in' ),
			'taxonomies'             => get_field( 'taxonomies' ),
			'relation'               => get_field( 'relation' ),
			'post_parent__in'        => get_field( 'post_parent__in' ),
			'orderby'                => get_field( 'orderby' ),
			'meta_key'               => get_field( 'meta_key' ),
			'order'                  => get_field( 'order' ),
			'exclude'                => get_field( 'exclude' ),
			'load_more'              => get_field( 'load_more' ),
			// Display.
			'show_image'             => get_field( 'show_image' ),
			'show_title'             => get_field( 'show_title' ),
			'show_header_meta'       => get_field( 'show_header_meta' ),
			'show_excerpt'           => get_field( 'show_excerpt' ),
			'show_content'           => get_field( 'show_content' ),
			'show_more_link'         => get_field( 'show_more_link' ),
			'show_footer_meta'       => get_field( 'show_footer_meta' ),
			'boxed'                  => get_field( 'boxed' ),

			'content_limit'          => get_field( 'content_limit' ),
			'image_size'             => get_field( 'image_size' ),
			'image_alignment'        => get_field( 'image_alignment' ),
			'more_link_text'         => get_field( 'more_link_text' ),
			'header_meta'            => get_field( 'header_meta' ),
			'footer_meta'            => get_field( 'footer_meta' ),
			// Layout.
			'template'               => get_field( 'template' ),
			'columns'                => get_field( 'columns' ),
			'align_cols'             => get_field( 'align_cols' ),
			'align_text'             => get_field( 'align_text' ),
			'grid_column_gap'        => get_field( 'grid_column_gap' ),
			'grid_row_gap'           => get_field( 'grid_row_gap' ),
		);

		// Sanitize.
		$this->values = array(
			// Query.
			'post_type'              => array_map( 'esc_attr', (array) $this->values['post_type'] ),
			'posts_per_page'         => absint( $this->values['posts_per_page'] ),
			'offset'                 => absint( $this->values['offset'] ),
			'query_by'               => $this->values['query_by'],
			'post__in'               => $this->values['post__in'],
			'taxonomies'             => $this->values['taxonomies'],
			'relation'               => $this->values['relation'],
			'post_parent__in'        => $this->values['post_parent__in'],
			'orderby'                => $this->values['orderby'],
			'meta_key'               => $this->values['meta_key'],
			'order'                  => $this->values['order'],
			'exclude'                => $this->values['exclude'],
			'load_more'              => (bool) $this->values['load_more'],
			// Display.
			// 'show'                => array_map( 'esc_attr', (array) $this->values['show'] ),
			'show_image'             => (bool) $this->values['show_image'],
			'show_title'             => (bool) $this->values['show_title'],
			'show_header_meta'       => (bool) $this->values['show_header_meta'],
			'show_excerpt'           => (bool) $this->values['show_excerpt'],
			'show_content'           => (bool) $this->values['show_content'],
			'show_more_link'         => (bool) $this->values['show_more_link'],
			'show_footer_meta'       => (bool) $this->values['show_footer_meta'],
			'boxed'                  => (bool) $this->values['boxed'],
			'content_limit'          => absint( $this->values['content_limit'] ),
			'image_size'             => esc_attr( $this->values['image_size'] ),
			'image_alignment'        => esc_attr( $this->values['image_alignment'] ),
			'more_link_text'         => esc_attr( $this->values['more_link_text'] ),
			'header_meta'            => esc_attr( $this->values['header_meta'] ),
			'footer_meta'            => esc_attr( $this->values['footer_meta'] ),
			// Layout.
			'template'               => $this->values['template'],
			'columns'                => $this->values['columns'],
			'align_cols'             => $this->values['align_cols'],
			'align_text'             => $this->values['align_text'],
			'grid_column_gap'        => $this->get_gap( esc_attr( $this->values['grid_column_gap'] ) ),
			'grid_row_gap'           => $this->get_gap( esc_attr( $this->values['grid_row_gap'] ) ),
		);

		// Defaults.
		$this->values['more_link_text'] = $this->values['more_link_text'] ?: __( 'Read More', 'mai-grid' );

		$loader = new Mai_Grid_Template_Loader;

		$posts = new WP_Query( $this->get_query_args() );

		if ( $posts->have_posts() ):

			// Load More.
			// if ( $this->values['load_more'] ) {
				// wp_enqueue_script( 'mai-grid-more', "{$this->base_url}/js/mai-grid-more{$this->suffix}.js", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/js/mai-grid-more{$this->suffix}.js" ) ) );
			// }

			$items   = $posts->post_count;
			$columns = absint( $this->values['columns'] );
			$rows    = absint( ceil( $items / $columns ) );
			$empty   = ( $columns * $rows ) - $items;

			printf( '<div class="mai-grid mai-grid-%s" style="--mai-grid-columns: %s;--mai-grid-column-gap: %s;--mai-grid-row-gap: %s;--mai-grid-empty: %s;">',
				sanitize_html_class( $this->values['template'] ),
				$this->values['columns'],
				$this->values['grid_column_gap'],
				$this->values['grid_row_gap'],
				$empty
			);

				while ( $posts->have_posts() ) : $posts->the_post();

					// Start them empty so we don't need isset() checks in the template.
					$data = array(
						'link'           => get_permalink(),
						'image'          => '',
						'title'          => '',
						'header_meta'    => '',
						'footer_meta'    => '',
						'content'        => '',
						'more_link'      => '',
						'more_link_text' => '',
						'boxed'          => false,
					);
					// Image.
					if ( $this->values['show_image'] ) {
						$image_id = get_post_thumbnail_id();
						if ( $image_id ) {
							// TODO: If 'default', find an actual image size.
							$image_size  = ( 'default' == $this->values['image_size'] ) ? 'thumbnail' : $this->values['image_size'];
							$image_align = 'none';
							$data['image'] = wp_get_attachment_image( $image_id, $image_size, false, array( 'class' => 'mai__grid-image' ) );
						}
					}
					// Title.
					if ( $this->values['show_title'] ) {
						$data['title'] = get_the_title();
					}
					// Header Meta.
					if ( $this->values['show_header_meta'] ) {
						$data['header_meta'] = do_shortcode( $this->values['header_meta'] );
					}
					// Excerpt.
					if ( $this->values['show_excerpt'] ) {
						$data['content'] = wpautop( get_the_excerpt() );
					}
					// Content.
					if ( $this->values['show_content'] ) {
						$data['content'] = strip_shortcodes( get_the_content() );
					}
					// Content Limit.
					if ( $this->values['content_limit'] > 0 ) {
						/**
						 * OLD WAY: Word count. Do we want this instead?
						 */
						// Reset the variable while trimming the content. wp_trim_words runs wp_strip_all_tags so we need to do this before re-processing.
						// $data['content'] = wp_trim_words( $data['content'], $this->values['content_limit'], '&hellip;' );

						/**
						 * NEW WAY: Character count. This matches Genesis content limit setting for archives, but I feel like word count makes more sense?
						 */
						$data['content'] = $this->get_the_content_limit( $data['content'], $this->values['content_limit'] );
					}
					// More Link.
					if ( $this->values['show_more_link'] ) {
						$data['more_link'] = $this->values['more_link_text'];
					}
					// Footer Meta.
					if ( $this->values['show_footer_meta'] ) {
						$data['footer_meta'] = do_shortcode( $this->values['footer_meta'] );
					}
					// Boxed.
					if ( $this->values['boxed'] ) {
						$data['boxed'] = true;
					}

					/**
					 * TODO: This is not loading correctly when you add a new grid block.
					 * The grid is empty until you toggle Show things.
					 */

					// Template.
					$loader->set_template_data( $data );
					$loader->get_template_part( $this->values['template'] );

				endwhile;

			echo '</div>';

		endif;
		wp_reset_postdata();

		// $template->get_template_part( 'standard' );
		// echo $this->get_grid_entries( $block );
	}

	/**
	 * Get the content, limited by max character count.
	 * Most of this was taking from Genesis get_the_content_limit() function.
	 *
	 * @param   string  $content         The existing content.
	 * @param   int     $max_characters  The character limit.
	 *
	 * @return  string  The limited content.
	 */
	function get_the_content_limit( $content, $max_characters ) {

		// Strip tags and shortcodes so the content truncation count is done correctly.
		$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );

		// Remove inline styles / scripts.
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char.
		$content = genesis_truncate_phrase( $content, $max_characters );

		// $output = sprintf( '<p>%s</p>', $content );
		$output = wpautop( $content . '&hellip;' );
		$link   = '';

		return apply_filters( 'get_the_content_limit', $output, $content, $link, $max_characters );
	}

	function get_query_args() {

		$args = array(
			'post_type'           => $this->values['post_type'],
			'posts_per_page'      => $this->values['posts_per_page'],
			'post_status'         => 'publish',
			'offset'              => $this->values['offset'],
			'ignore_sticky_posts' => true,
		);

		// Handle query_by.
		switch ( $this->values['query_by'] ) {
			// case 'date':
				// $args = 'else';
				// break;
			case 'parent':
				$args['post_parent__in'] = $this->values['post_parent__in'];
				break;
			case 'title':
				$args['post__in'] = $this->values['post__in'];
				break;
			case 'taxonomy':
				$args['tax_query'] = array(
					'relation' => $this->values['relation'],
				);
				foreach( $this->values['taxonomies'] as $taxo ) {
					$args['tax_query'][] = array(
						'taxonomy' => $taxo['taxonomy'],
						'field'    => 'id',
						'terms'    => $taxo['terms'],
						'operator' => $taxo['operator'],
					);
				}
				break;
		}

		// vd( $args );

		return apply_filters( 'mai_post_grid_args', $args, $this->block );
	}

	function get_gap( $value ) {
		if ( is_numeric( $value ) ) {
			return sprintf( '%spx', intval( $value ) );
		}
		return trim( $value );
		// switch ( $value ) {
		// 	case 'none':
		// 		$gap = '0';
		// 		break;
		// 	case 'xs':
		// 		$gap = '8px';
		// 		break;
		// 	case 'sm':
		// 		$gap = '16px';
		// 		break;
		// 	case 'md':
		// 		$gap = '24px';
		// 		break;
		// 	case 'lg':
		// 		$gap = '36px';
		// 		break;
		// 	case 'xl':
		// 		$gap = '52px';
		// 		break;
		// 	default:
		// 		$gap = '0';
		// }
		// return $gap;
	}

	function filters() {
		/**
		 * Query.
		 */
		// Post Types.
		add_filter( "acf/load_field/key={$this->fields['post_type']}", array( $this, 'load_post_types' ) );
		// Get Entries By.
		add_filter( "acf/load_field/key={$this->fields['query_by']}", array( $this, 'load_query_by' ) );
		// Entries.
		add_filter( "acf/fields/post_object/query/key={$this->fields['post__in']}", array( $this, 'get_posts' ), 10, 1 );
		// Taxonomy.
		add_filter( "acf/load_field/key={$this->fields['taxonomy']}", array( $this, 'load_taxonomies' ) );
		// Terms.
		add_filter( "acf/fields/taxonomy/query/key={$this->fields['terms']}", array( $this, 'get_terms' ), 10, 1 );
		// Operator.
		add_filter( "acf/load_field/key={$this->fields['operator']}", array( $this, 'load_operators' ) );
		// Parent.
		add_filter( "acf/fields/post_object/query/key={$this->fields['parent']}", array( $this, 'get_parents' ), 10, 1 );
		// add_filter( "acf/load_field/key={$this->fields['parent']}", array( $this, 'load_parents' ) );
		// Exclude Content.
		add_filter( "acf/load_field/key={$this->fields['exclude']}", array( $this, 'load_exclude' ) );
		// Order By.
		add_filter( "acf/load_field/key={$this->fields['orderby']}", array( $this, 'load_orderby' ) );
		// Order.
		add_filter( "acf/load_field/key={$this->fields['order']}", array( $this, 'load_order' ) );

		/**
		 * Display.
		 */
		// add_filter( "acf/load_field/key={$this->fields['show_image']}",     array( $this, 'load_show_image' ) );
		// add_filter( "acf/load_field/key={$this->fields['show_title']}",     array( $this, 'load_show_title' ) );
		// add_filter( "acf/load_field/key={$this->fields['show_date']}",      array( $this, 'load_show_date' ) );
		// add_filter( "acf/load_field/key={$this->fields['show_author']}",    array( $this, 'load_show_author' ) );
		// Image Size.
		add_filter( "acf/load_field/key={$this->fields['image_size']}",     array( $this, 'load_image_sizes' ) );
		// Image Alignment.
		add_filter( "acf/load_field/key={$this->fields['image_align']}",    array( $this, 'load_image_align' ) );
		// More Link Text.
		add_filter( "acf/load_field/key={$this->fields['more_link_text']}", array( $this, 'load_more_link_text' ) );

		/**
		 * Layout.
		 */
		// Template.
		add_filter( "acf/load_field/key={$this->fields['template']}", array( $this, 'load_templates' ) );
		// Columns.
		add_filter( "acf/load_field/key={$this->fields['columns']}", array( $this, 'load_columns' ) );
		// Align Columns.
		add_filter( "acf/load_field/key={$this->fields['align_cols']}", array( $this, 'load_align_columns' ) );
		// Align Text.
		add_filter( "acf/load_field/key={$this->fields['align_text']}", array( $this, 'load_align_text' ) );
		// Gaps.
		// add_filter( "acf/load_field/key={$this->fields['grid_column_gap']}", array( $this, 'load_gap' ) );
		// add_filter( "acf/load_field/key={$this->fields['grid_row_gap']}", array( $this, 'load_gap' ) );

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
				// TODO: JS to get template value and set defaults? Too aggressive?
				// $field['default'] = '';
				return $field;
			});
		}
		// Conditionals.
		foreach( $this->get_fields() as $name => $key ) {
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

		$templates = $this->get_templates();

		if ( ! $templates ) {
			return $field;
		}

		foreach( $templates as $name => $template ) {
			$field['choices'][ $name ] = $template['label'];
		}

		// TODO: Filter via config or something?!?

		$field['default_value'] = 'standard';

		return $field;
	}

	/**
	 * Load Post Types.
	 */
	function load_post_types( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$post_types = get_post_types( array(
			'public'             => true,
			'publicly_queryable' => true,
		), 'objects', 'or' );

		if ( $post_types ) {
			foreach ( $post_types as $name => $post_type ) {
				$field['choices'][ $name ] = $post_type->label;
			}
		}

		$field['default_value'] = 'post';
		return $field;
	}

	function load_query_by( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'date'     => __( 'Date', 'mai-grid' ),
			'title'    => __( 'Title', 'mai-grid' ),
			'taxonomy' => __( 'Taxonomy', 'mai-grid' ),
			'parent'   => __( 'Parent', 'mai-grid' ),
		);

		$field['default_value'] = 'date';

		return $field;
	}

	function get_posts( $args ) {
		if ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {
			$args['post_type'] = $_REQUEST['post_type'];
		}
		return $args;
	}

	function load_taxonomies( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		if ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {

			$taxonomies = get_object_taxonomies( $_REQUEST['post_type'], 'objects' );

			if ( $taxonomies ) {
				foreach ( $taxonomies as $name => $taxo ) {
					$field['choices'][ $name ] = $taxo->label;
				}
			}
		}

		return $field;
	}

	function get_terms( $args ) {
		if ( isset( $_REQUEST['taxonomy'] ) && ! empty( $_REQUEST['taxonomy'] ) ) {
			$args['taxonomy'] = $_REQUEST['taxonomy'];
		}
		return $args;
	}

	function load_operators( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'IN'     => __( 'In', 'mai-grid' ),
			'NOT IN' => __( 'Not In', 'mai-grid' ),
		);

		$field['default_value'] = 'IN';

		return $field;
	}

	function get_parents( $args ) {
		if ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {
			$args['post_type'] = $_REQUEST['post_type'];
		}
		return $args;
	}

	function load_parents( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		// Bail if no post type.
		if ( ! ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) ) {
			return $field;
		}

		$posts = acf_get_grouped_posts( array(
			'post_type'   => $_REQUEST['post_type'],
			'post_status' => 'publish',
		) );

		if ( ! $posts ) {
			return $field;
		}

		$field['choices'] = $posts;

		// foreach( $posts as $post ) {
			// $field['choices'][ $post->ID ] = acf_get_post_title( $post->ID );
		// }

		return $field;
	}

	function load_exclude( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'exclude_current'   => __( 'Exclude current', 'mai-grid' ),
			'exclude_displayed' => __( 'Exclude displayed', 'mai-grid' ),
		);

		return $field;
	}

	function load_orderby( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'title'          => __( 'Title', 'mai-grid' ),
			'name'           => __( 'Slug', 'mai-grid' ),
			'date'           => __( 'Date', 'mai-grid' ),
			'modified'       => __( 'Modified', 'mai-grid' ),
			'rand'           => __( 'Random', 'mai-grid' ),
			'comment_count'  => __( 'Comment Count', 'mai-grid' ),
			'menu_order'     => __( 'Menu Order', 'mai-grid' ),
			'post__in'       => __( 'Entries Order', 'mai-grid' ),
			'meta_value_num' => __( 'Meta Value Number', 'mai-grid' ),
		);

		$field['default_value'] = 'date';

		return $field;
	}

	function load_order( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'ASC'  => __( 'Ascending', 'mai-grid' ),
			'DESC' => __( 'Descending', 'mai-grid' ),
		);

		return $field;
	}

	/**
	 * DONE?: Show options should be individual fields.
	 * 1. We can have conditional fields like image size right under Image checkbox, content limit under content/excerpt, and more link text under more link.
	 * 2. We can use a grid template config to decide which fields are enabled/allowed.
	 */

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
			''       => __( 'Full Width', 'mai-grid' ),
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
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			''       => __( 'None', 'mai-grid' ),
			'left'   => '<span class="dashicons dashicons-editor-alignleft"></span>',
			'center' => $this->get_align_cols_center(),
			'right'  => '<span class="dashicons dashicons-editor-alignright"></span>',
		);

		return $field;
	}

	function load_align_text( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		// TODO: If 'background' is the layout, can we do vertical alignment here too?!?!

		// $field['choices'] = array(
		// 	''       => __( 'None', 'mai-grid' ),
		// 	'left'   => __( 'Left', 'mai-grid' ),
		// 	'center' => __( 'Center', 'mai-grid' ),
		// 	'right'  => __( 'Right', 'mai-grid' ),
		// );

		$field['choices'] = array(
			''       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M634 471L36 3.5A16 16 0 0 0 13.49 6l-10 12.5A16 16 0 0 0 6 41l598 467.5a16 16 0 0 0 22.5-2.5l10-12.5A16 16 0 0 0 634 471zM528 296h-39.94l52.69 41.19A15.6 15.6 0 0 0 544 328v-16a16 16 0 0 0-16-16zm16-112a16 16 0 0 0-16-16H324.34l61.39 48H528a16 16 0 0 0 16-16zm-16-96a16 16 0 0 0 16-16V56a16 16 0 0 0-16-16H160.61L222 88zM112 424a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16h367.37L418 424zm0-80h203.65l-61.39-48H112a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16z"/></svg>',
			'left'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M12.83 344h262.34A12.82 12.82 0 0 0 288 331.17v-22.34A12.82 12.82 0 0 0 275.17 296H12.83A12.82 12.82 0 0 0 0 308.83v22.34A12.82 12.82 0 0 0 12.83 344zm0-256h262.34A12.82 12.82 0 0 0 288 75.17V52.83A12.82 12.82 0 0 0 275.17 40H12.83A12.82 12.82 0 0 0 0 52.83v22.34A12.82 12.82 0 0 0 12.83 88zM432 168H16a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16v-16a16 16 0 0 0-16-16zm0 256H16a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16v-16a16 16 0 0 0-16-16z"/></svg>',
			'center' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M108.1 88h231.81A12.09 12.09 0 0 0 352 75.9V52.09A12.09 12.09 0 0 0 339.91 40H108.1A12.09 12.09 0 0 0 96 52.09V75.9A12.1 12.1 0 0 0 108.1 88zM432 424H16a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16v-16a16 16 0 0 0-16-16zm0-256H16a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16v-16a16 16 0 0 0-16-16zm-92.09 176A12.09 12.09 0 0 0 352 331.9v-23.81A12.09 12.09 0 0 0 339.91 296H108.1A12.09 12.09 0 0 0 96 308.09v23.81a12.1 12.1 0 0 0 12.1 12.1z"/></svg>',
			'right'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M16 216h416a16 16 0 0 0 16-16v-16a16 16 0 0 0-16-16H16a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16zm416 208H16a16 16 0 0 0-16 16v16a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16v-16a16 16 0 0 0-16-16zm3.17-384H172.83A12.82 12.82 0 0 0 160 52.83v22.34A12.82 12.82 0 0 0 172.83 88h262.34A12.82 12.82 0 0 0 448 75.17V52.83A12.82 12.82 0 0 0 435.17 40zm0 256H172.83A12.82 12.82 0 0 0 160 308.83v22.34A12.82 12.82 0 0 0 172.83 344h262.34A12.82 12.82 0 0 0 448 331.17v-22.34A12.82 12.82 0 0 0 435.17 296z"/></svg>',
		);

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
			1 => __( 'None', 'mai-grid' ),
			2 => __( '2', 'mai-grid' ),
			3 => __( '3', 'mai-grid' ),
			4 => __( '4', 'mai-grid' ),
			5 => __( '5', 'mai-grid' ),
			6 => __( '6', 'mai-grid' ),
		);

		$field['default_value'] = 3;

		return $field;
	}

	// function load_gap( $field ) {

	// 	// Start empty.
	// 	$field['choices'] = array();

	// 	// Keep admin clean.
	// 	// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
	// 		// return $field;
	// 	// }

	// 	$field['choices'] = array(
	// 		'none' => __( 'None', 'mai-grid' ),
	// 		// 'xxxs' => __( 'XXX-Small', 'mai-grid' ),
	// 		// 'xxs'  => __( 'XX-Small', 'mai-grid' ),
	// 		'xs'   => __( 'XS', 'mai-grid' ),
	// 		'sm'   => __( 'S', 'mai-grid' ),
	// 		'md'   => __( 'M', 'mai-grid' ),
	// 		'lg'   => __( 'L', 'mai-grid' ),
	// 		'xl'   => __( 'XL', 'mai-grid' ),
	// 		// 'xxl'  => __( 'XX-Large', 'mai-grid' ),
	// 	);

	// 	$field['default_value'] = 'md';

	// 	return $field;
	// }

	function load_more_link_text( $field ) {
		$field['placeholder'] = __( 'Read More', 'mai-grid' );
		return $field;
	}

	function add_conditional_logic( $field ) {

		// Build conditions.
		$conditions = array();
		foreach( $this->get_templates() as $template_name => $template_values ) {
			// Skip if not removing anything.
			if ( ! isset( $template_values['remove_support'] ) || empty( $template_values['remove_support'] ) ) {
				continue;
			}
			// Skip if not a field we are removing.
			if ( ! in_array( $field['name'], $template_values['remove_support'] ) ) {
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

	/**
	 * TODO: explicitely declare support for every setting in Display tab. It's too confusing to guess right now.
	 * TODO: defaults won't work with PHP. Needs to be JS, but that would change the actual values, not the default.
	 * Maybe we don't do this.
	 *
	 */
	function get_templates() {
		return array(
			'standard' => array(
				'label'          => __( 'Standard', 'mai-grid' ),
				'remove_support' => array(
					'align_text_vertical',
				),
			),
			'background' => array(
				'label'          => __( 'Background Image', 'mai-grid' ),
				'remove_support' => array(
					'image_align',
					'show_excerpt',
					'show_content',
					'show_header_meta',
					'show_footer_meta',
					'show_more_link',
					'boxed',
				),
			),
			'compact' => array(
				'label'          => __( 'Compact', 'mai-grid' ),
				'remove_support' => array(
					'image_align',
					'show_excerpt',
					'show_content',
					'show_header_meta',
					'show_footer_meta',
					'show_more_link',
					'boxed',
				),
				// 'defaults' => array(
				// 	'show_image' => true,
				// 	'image_size' => 'tiny',
				// 	'show_title' => true,
				// ),
			),
		);
	}

	function get_fields() {
		return array_merge(
			$this->get_display_fields(),
			$this->get_layout_fields(),
			$this->get_wp_query_fields(),
			$this->get_wp_term_query_fields(),
		);
	}

	function get_wp_query_fields() {
		return array(
			'post_type'      => 'field_5df1053632ca2',
			'posts_per_page' => 'field_5df1053632ca8',
			'offset'         => 'field_5df1bf01ea1de',
			'query_by'       => 'field_5df1053632cad',
			'post__in'       => 'field_5df1053632cbc',
			'taxonomies'     => 'field_5df1397316270',
			'taxonomy'       => 'field_5df1398916271',
			'terms'          => 'field_5df139a216272',
			'relation'       => 'field_5df139281626f',
			'operator'       => 'field_5df18f2305c2c',
			'relation'       => 'field_5df139281626f',
			'parent'         => 'field_5df1053632ce4',
			'orderby'        => 'field_5df1053632cec',
			'meta_key'       => 'field_5df1053632cf4',
			'order'          => 'field_5df1053632cfb',
			'exclude'        => 'field_5df1053632d03',
			'load_more'      => 'field_5df1bbaeb25f2',
		);
	}

	function get_wp_term_query_fields() {
		return array(

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
			'footer_meta'         => 'field_5e2b563a7c6cf',
			'boxed'               => 'field_5e2a08a182c2c',
			'align_text'          => 'field_5c853f84eacd6',
			'align_text_vertical' => 'field_5e2f519edc912',
		);
	}

	/**
	 * TODO: Move boxed to a new "Style" heading.
	 * Add Overlay for background.
	 * Any others we need? Content fade in for background template? Too specific, and should be CSS in the theme?
	 */

	function get_layout_fields() {
		return array(
			'columns'         => 'field_5c854069d358c',
			'align_cols'      => 'field_5c853e6672972',
			'grid_column_gap' => 'field_5c8542d6a67c5',
			'grid_row_gap'    => 'field_5e29f1785bcb6',
		);
	}

	function get_align_cols_icon() {
		ob_start();
		?>
		<svg xmlns:x="http://ns.adobe.com/Extensibility/1.0/" xmlns:i="http://ns.adobe.com/AdobeIllustrator/10.0/"
			xmlns:graph="http://ns.adobe.com/Graphs/1.0/" xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 125"
			enable-background="new 0 0 100 100" xml:space="preserve">
			<switch>
				<foreignObject requiredExtensions="http://ns.adobe.com/AdobeIllustrator/10.0/" x="0" y="0" width="1" height="1" />
				<g i:extraneous="self">
					<g>
						<rect x="38" y="20" fill="#000000" width="23" height="16.6" />
						<rect x="66" y="20" fill="#000000" width="24" height="16.6" />
						<rect x="10" y="41.6" fill="#000000" width="23" height="16.6" />
						<rect x="66" y="41.6" fill="#000000" width="24" height="16.6" />
						<rect x="10" y="20" fill="#000000" width="23" height="16.6" />
						<rect x="10" y="63.1" fill="#000000" width="23" height="16.9" />
						<rect x="38" y="41.6" fill="#000000" width="23" height="16.6" />
						<rect x="38" y="63.1" fill="#000000" width="23" height="16.9" />
						<rect x="66" y="63.1" fill="#000000" width="24" height="16.9" />
					</g>
				</g>
			</switch>
		</svg>
		<?php
		return ob_get_clean();
	}

	function get_align_cols_center() {
		ob_start();
		?>
		<svg viewBox="0 0 64 42" xmlns="http://www.w3.org/2000/svg">
			<rect x="0" y="0" width="20" height="20" rx="1" style="fill:#000;" />
			<rect x="22" y="0" width="20" height="20" rx="1" style="fill:#000;" />
			<rect x="44" y="0" width="20" height="20" rx="1" style="fill:#000;" />
			<rect x="11" y="22" width="20" height="20" rx="1" style="fill:#000;" />
			<rect x="33" y="22" width="20" height="20" rx="1" style="fill:#000;" />
		</svg>
		<?php
		return ob_get_clean();
	}

}


function mai_grid_write_to_file( $value ) {
	/**
	 * This function for testing & debuggin only.
	 * Do not leave this function working on your site.
	 */
	$file   = dirname( __FILE__ ) . '/__data.txt';
	$handle = fopen( $file, 'a' );
	ob_start();
	if ( is_array( $value ) || is_object( $value ) ) {
		print_r( $value );
	} elseif ( is_bool( $value ) ) {
		var_dump( $value );
	} else {
		echo $value;
	}
	echo "\r\n\r\n";
	fwrite( $handle, ob_get_clean() );
	fclose( $handle );
}
