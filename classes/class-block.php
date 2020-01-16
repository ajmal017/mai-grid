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
			'render_callback' => array( $this, 'do_grid_entries' ),
			'enqueue_assets'  => array( $this, 'enqueue_assets'),
			'supports'        => array(
				'align'  => array( 'wide' ),
				'ancher' => true,
			),
		) );
	}

	function do_grid_entries( $block, $content = '', $is_preview = false ) {

		$this->block = $block;

		// Get the values.
		$this->values = array(
			// Query.
			'post_type'      => get_field( 'post_type' ),
			'posts_per_page' => get_field( 'posts_per_page' ),
			'offset'         => get_field( 'offset' ),
			'query_by'       => get_field( 'query_by' ),
			'post__in'       => get_field( 'post__in' ),
			'taxonomies'     => get_field( 'taxonomies' ),
			'relation'       => get_field( 'relation' ),
			'parent'         => get_field( 'parent' ),
			'orderby'        => get_field( 'orderby' ),
			'meta_key'       => get_field( 'meta_key' ),
			'order'          => get_field( 'order' ),
			'exclude'        => get_field( 'exclude' ),
			'load_more'      => get_field( 'load_more' ),
			// Display.
			'show_image'             => get_field( 'show_image' ),
			'show_title'             => get_field( 'show_title' ),
			'show_date'              => get_field( 'show_date' ),
			'show_author'            => get_field( 'show_author' ),
			'show_excerpt'           => get_field( 'show_excerpt' ),
			'show_content'           => get_field( 'show_content' ),
			'show_entry_meta_header' => get_field( 'show_entry_meta_header' ),
			'show_entry_meta_footer' => get_field( 'show_entry_meta_footer' ),
			'show_more_link'         => get_field( 'show_more_link' ),

			'content_limit'  => get_field( 'content_limit' ),
			'image_size'     => get_field( 'image_size' ),
			'more_link_text' => get_field( 'more_link_text' ),
			// Layout.
			'template'       => get_field( 'template' ),
			'columns'        => get_field( 'columns' ),
			'align_cols'     => get_field( 'align_cols' ),
			'align_text'     => get_field( 'align_text' ),
			'gutter'         => get_field( 'gutter' ),
		);

		// Sanitize.
		$this->values = array(
			// Query.
			'post_type'      => array_map( 'esc_attr', (array) $this->values[ 'post_type' ] ),
			'posts_per_page' => absint( $this->values[ 'posts_per_page' ] ),
			'offset'         => absint( $this->values[ 'offset' ] ),
			'query_by'       => $this->values[ 'query_by' ],
			'post__in'       => $this->values[ 'post__in' ],
			'taxonomies'     => $this->values[ 'taxonomies' ],
			'relation'       => $this->values[ 'relation' ],
			'parent'         => $this->values[ 'parent' ],
			'orderby'        => $this->values[ 'orderby' ],
			'meta_key'       => $this->values[ 'meta_key' ],
			'order'          => $this->values[ 'order' ],
			'exclude'        => $this->values[ 'exclude' ],
			'load_more'      => (bool) $this->values[ 'load_more' ],
			// Display.
			// 'show'           => array_map( 'esc_attr', (array) $this->values[ 'show' ] ),
			'show_image'             => (bool) $this->values[ 'show_image' ],
			'show_title'             => (bool) $this->values[ 'show_title' ],
			'show_date'              => (bool) $this->values[ 'show_date' ],
			'show_author'            => (bool) $this->values[ 'show_author' ],
			'show_excerpt'           => (bool) $this->values[ 'show_excerpt' ],
			'show_content'           => (bool) $this->values[ 'show_content' ],
			'show_entry_meta_header' => (bool) $this->values[ 'show_entry_meta_header' ],
			'show_entry_meta_footer' => (bool) $this->values[ 'show_entry_meta_footer' ],
			'show_more_link'         => (bool) $this->values[ 'show_more_link' ],
			'content_limit'  => absint( $this->values[ 'content_limit' ] ),
			'image_size'     => esc_attr( $this->values[ 'image_size' ] ),
			'more_link_text' => esc_attr( $this->values[ 'more_link_text' ] ),
			// Layout.
			'template'       => $this->values[ 'template' ],
			'columns'        => $this->values[ 'columns' ],
			'align_cols'     => $this->values[ 'align_cols' ],
			'align_text'     => $this->values[ 'align_text' ],
			'gutter'         => $this->values[ 'gutter' ],
		);

		// Defaults.
		$this->values['more_link_text'] = $this->values[ 'more_link_text' ] ?: __( 'Read More', 'mai-grid' );

		$loader = new Mai_Grid_Template_Loader;

		$posts = new WP_Query( $this->get_query_args() );

		if ( $posts->have_posts() ):

			// Template.
			if ( file_exists( "{$this->base_dir}/css/mai-grid-{$this->values['template']}{$this->suffix}.css" ) ) {
				wp_enqueue_style( "mai-grid-{$this->values['template']}", "{$this->base_url}/css/mai-grid-{$this->values['template']}{$this->suffix}.css", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/css/mai-grid-{$this->values['template']}{$this->suffix}.css" ) ) );
			}

			// vd( $this->values );

			// Load More.
			// if ( $this->values['load_more'] ) {
				// wp_enqueue_script( 'mai-grid-more', "{$this->base_url}/js/mai-grid-more{$this->suffix}.js", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/js/mai-grid-more{$this->suffix}.js" ) ) );
			// }

			$items   = $posts->post_count;
			$columns = absint( $this->values['columns'] );
			$rows    = absint( ceil( $items / $columns ) );
			$empty   = ( $columns * $rows ) - $items;

			printf( '<div class="mai-grid mai-grid-%s" style="--mai-grid-columns: %s;--mai-grid-gutter: %s;--mai-grid-empty: %s;">',
				sanitize_html_class( $this->values['template'] ),
				$this->values['columns'],
				'16px',
				$empty
			);

				while ( $posts->have_posts() ) : $posts->the_post();

					// Start them empty so we don't need isset() checks in the template.
					$data = array(
						'link'           => get_permalink(),
						'image'          => '',
						'title'          => '',
						'date'           => '',
						'author'         => '',
						'content'        => '',
						'more_link'      => '',
						'more_link_text' => '',
					);
					// Image.
					if ( $this->values['show_image'] ) {
						$image_id = get_post_thumbnail_id();
						if ( $image_id ) {
							// TODO: If 'default', find an actual image size.
							$image_size = ( 'default' == $this->values['image_size'] ) ? 'thumbnail' : $this->values['image_size'];
							$data['image'] = wp_get_attachment_image( $image_id, $image_size, false, array( 'class' => 'mai__grid-image' ) );
						}
					}
					// Title.
					if ( $this->values['show_title'] ) {
						$data['title'] = get_the_title();
					}
					// Date.
					if ( $this->values['show_date'] ) {
						$data['date'] = '';
					}
					// Author.
					if ( $this->values['show_author'] ) {
						$data['author'] = '';
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
						// Reset the variable while trimming the content. wp_trim_words runs wp_strip_all_tags so we need to do this before re-processing.
						$data['content'] = wp_trim_words( $data['content'], $this->values['content_limit'], '&hellip;' );
					}
					// Entry Meta.
					if ( $this->values['show_entry_meta_footer'] ) {
						$data['entry_meta_footer'] = '';
					}
					// More Link.
					if ( $this->values['show_more_link'] ) {
						$data['more_link'] = $this->values['more_link_text'];
					}
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

	function get_query_args() {

		$args = array(
			'post_type'      => $this->values['post_type'],
			'posts_per_page' => $this->values['posts_per_page'],
			'post_status'    => 'publish',
			'offset'         => $this->values['offset'],
		);

		// Handle query_by.
		switch ( $this->values['query_by'] ) {
			// case 'date':
				// $args = 'else';
				// break;
			case 'parent':
				$args['parent'] = $this->values['parent'];
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

		// vd( $this->values );

		return apply_filters( 'mai_post_grid_args', $args, $this->block );
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
		// Exclude Content.
		add_filter( "acf/load_field/key={$this->fields['exclude']}", array( $this, 'load_exclude' ) );
		// Order By.
		add_filter( "acf/load_field/key={$this->fields['orderby']}", array( $this, 'load_orderby' ) );
		// Order.
		add_filter( "acf/load_field/key={$this->fields['order']}", array( $this, 'load_order' ) );

		/**
		 * Display.
		 */
		// Show.
		foreach( $this->get_show_fields() as $name => $key ) {
			add_filter( "acf/load_field/key={$key}", function( $field ) {
				$field['label'] = '';
				return $field;
			});
		}
		add_filter( "acf/load_field/key={$this->fields['show_image']}",     array( $this, 'load_show_image' ) );
		add_filter( "acf/load_field/key={$this->fields['show_title']}",     array( $this, 'load_show_title' ) );
		add_filter( "acf/load_field/key={$this->fields['show_date']}",      array( $this, 'load_show_date' ) );
		add_filter( "acf/load_field/key={$this->fields['show_author']}",    array( $this, 'load_show_author' ) );
		// Image Size.
		add_filter( "acf/load_field/key={$this->fields['image_size']}",     array( $this, 'load_image_sizes' ) );
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
		// Gutter.
		add_filter( "acf/load_field/key={$this->fields['gutter']}", array( $this, 'load_gutter' ) );
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
	 * TODO: Show options should be individual fields.
	 * 1. We can have conditional fields like image size right under Image checkbox, content limit under content/excerpt, and more link text under more link.
	 * 2. We can use a grid template config to decide which fields are enabled/allowed.
	 */


	/**
	 * TODO:
	 * These "show" fields should set defaults based on Template field.
	 */

	/**
	 * Load Show Image.
	 */
	function load_show_image( $field ) {
		// TODO: Make this a helper method.
		$field['default_value'] = true;
		$conditions = array();
		foreach( $this->get_templates() as $name => $values ) {
			// Bail if this field is supported.
			if ( in_array( $field['name'], $values['supports'] ) ) {
				continue;
			}
			$conditions[] = array(
				'field'    => $this->fields[ $field['name'] ],
				'operator' => '!=',
				'value'    => $name,
			);
		}
		// If existing conditional logic.
		if ( $field['conditional_logic'] ) {
			// Loop through and add this condition to each.
			foreach( $field['conditional_logic'] as $cl_index => $cl_values ) {
				$field['conditional_logic'][$cl_index] = array_merge( $field['conditional_logic'][$cl_index], $conditions );
			}
		}
		// No existing conditions.
		else {
			$field['conditional_logic'] = array( $conditions );
		}
		// vdd( $field );
		return $field;
	}
	/**
	 * Load Show Title.
	 */
	function load_show_title( $field ) {
		$field['default_value'] = true;
		return $field;
	}
	/**
	 * Load Show Date.
	 */
	function load_show_date( $field ) {
		$field['default_value'] = false;
		return $field;
	}
	/**
	 * Load Show Author.
	 */
	function load_show_author( $field ) {
		$field['default_value'] = false;
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

	function load_align_columns( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			''       => __( 'None', 'mai-grid' ),
			'left'   => __( 'Left', 'mai-grid' ),
			'center' => __( 'Center', 'mai-grid' ),
			'right'  => __( 'Right', 'mai-grid' ),
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

		$field['choices'] = array(
			''       => __( 'None', 'mai-grid' ),
			'left'   => __( 'Left', 'mai-grid' ),
			'center' => __( 'Center', 'mai-grid' ),
			'right'  => __( 'Right', 'mai-grid' ),
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

	/**
	 * TODO: Should we have a bottom margin setting? xs/sm/md/etc or px value?
	 */

	function load_gutter( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		// if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			// return $field;
		// }

		$field['choices'] = array(
			'none' => __( 'None', 'mai-grid' ),
			// 'xxxs' => __( 'XXX-Small', 'mai-grid' ),
			// 'xxs'  => __( 'XX-Small', 'mai-grid' ),
			'xs'   => __( 'XS', 'mai-grid' ),
			'sm'   => __( 'S', 'mai-grid' ),
			'md'   => __( 'M', 'mai-grid' ),
			'lg'   => __( 'L', 'mai-grid' ),
			'xl'   => __( 'XL', 'mai-grid' ),
			// 'xxl'  => __( 'XX-Large', 'mai-grid' ),
		);

		$field['default_value'] = 'md';

		return $field;
	}

	function load_more_link_text( $field ) {
		$field['placeholder'] = __( 'Read More', 'mai-grid' );
		return $field;
	}

	function enqueue_assets() {

		if ( is_admin() ) {

			wp_enqueue_script( 'mai-acf-wp-query', "{$this->base_url}/js/mai-acf-wp-query{$this->suffix}.js", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/js/mai-acf-wp-query{$this->suffix}.js" ) ), true );
			wp_localize_script( 'mai-acf-wp-query', 'maiACFWPQueryVars', array(
				'fields' => $this->get_wp_query_fields(),
				'keys'   => array_values( $this->get_wp_query_fields() ),
			) );

			wp_enqueue_script( 'mai-grid-entries-admin', "{$this->base_url}/js/mai-grid-admin{$this->suffix}.js", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/js/mai-grid-admin{$this->suffix}.js" ) ), true );
			wp_localize_script( 'mai-grid-entries-admin', 'maiGridVars', $this->get_templates() );
		}

		// wp_enqueue_style( 'mai-grid', "{$this->base_url}/css/mai-grid-entries{$this->suffix}.css", array(), $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/css/mai-grid-entries{$this->suffix}.css" ) ) );
	}

	function get_templates() {
		return array(
			'standard' => array(
				'label'    => __( 'Standard', 'mai-grid' ),
				// 'style'    => "{$this->base_url}/js/mai-acf-wp-query{$this->suffix}.js",
				'supports' => array(
					'show_image',
					'show_title',
					'show_date',
					'show_author',
					'show_excerpt',
					'show_content',
					'show_entry_meta_header',
					'show_entry_meta_footer',
					'show_more_link',
				),
			),
			'background' => array(
				'label'    => __( 'Background', 'mai-grid' ),
				'supports' => array(
					// 'show_image',
					'show_title',
					// 'show_more_link',
				),
			),
			'compact' => array(
				'label'    => __( 'Compact', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'show_title',
					// 'show_more_link',
				),
				'defaults' => array(
					'show_image' => true,
					'image_size' => 'tiny',
					'show_title' => true,
				),
			),
		);
	}

	function get_fields() {
		return array_merge(
			$this->get_wp_query_fields(),
			$this->get_wp_term_query_fields(),
			$this->get_show_fields(),
			$this->get_display_fields(),
			$this->get_layout_fields(),
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

	function get_show_fields() {
		return array(
			'show_image'             => 'field_5e1e665ffc7e5',
			'show_title'             => 'field_5e1e6693fc7e6',
			'show_date'              => 'field_5e1e67c0e9889',
			'show_author'            => 'field_5e1e67d7e988a',
			'show_excerpt'           => 'field_5e1e67e7e988b',
			'show_content'           => 'field_5e1e67fce988c',
			'show_entry_meta_header' => 'field_5e1e680ce988d',
			'show_entry_meta_footer' => 'field_5e1e6835e988e',
			'show_more_link'         => 'field_5e1e6843e988f',
		);
	}

	function get_display_fields() {
		return array(
			'content_limit'  => 'field_5bd51ac107244',
			'image_size'     => 'field_5bd50e580d1e9',
			'more_link_text' => 'field_5c85465018395',
		);
	}

	function get_layout_fields() {
		return array(
			'template'   => 'field_5de9b96fb69b0',
			'columns'    => 'field_5c854069d358c',
			'align_cols' => 'field_5c853e6672972',
			'align_text' => 'field_5c853f84eacd6',
			'gutter'     => 'field_5c8542d6a67c5',
		);
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
