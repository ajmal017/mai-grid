<?php

// Get it started.
add_action( 'plugins_loaded', function() {
	new Mai_Grid_Entries_Block;
});

class Mai_Grid_Entries_Block {

	protected $fields;

	function __construct() {

		$this->field_keys  = $this->get_fields();
		$this->field_names = array_flip( $this->field_keys );

		// add_action( 'acf/input/admin_head',                   array( $this, 'custom_css' ) );
		add_action( 'acf/init', array( $this, 'register_block' ), 10, 3 );
		$this->filters();
		// add_filter( 'acf/load_field/key=field_5dd6bca5fa5c6', array( $this, 'load_type_choices' ) );
		// add_filter( 'acf/load_field/key=field_5dd6c75b0ea87', array( $this, 'load_icon_choices' ) );
	}

	function get_fields() {
		return array(
			// Content.
			'content'         => 'field_5bd3ea2224a92',
			'number'          => 'field_5bd51bc0296d2',
			'query_by'        => 'field_5bd530b715aaf',
			'ids'             => 'field_5bd3ef320bc3a',
			'taxonomy'        => 'field_5bd51d7e47d05',
			'terms'           => 'field_5bd524aa95dbe',
			'parent'          => 'field_5c85592ee744b',
			'orderby'         => 'field_5c852d15e2507',
			'meta_key'        => 'field_5c8533819bd67',
			'order'           => 'field_5c852df4e2508',
			'exclude_content' => 'field_5c853a6eff47d',
			// Display.
			'show'            => 'field_5bd3eae824a93',
			'content_limit'   => 'field_5bd51ac107244',
			'image_size'      => 'field_5bd50e580d1e9',
			'more_link_text'  => 'field_5c85465018395',
			// Layout.
			'template'        => 'field_5de9b96fb69b0',
			'columns'         => 'field_5c854069d358c',
			'align_columns'      => 'field_5c853e6672972',
			'align_text'      => 'field_5c853f84eacd6',
			'gutter'          => 'field_5c8542d6a67c5',
		);
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

		$data = array(
			'content'    => get_field( 'content' ),
			'number'     => get_field( 'number' ),
			'columns'    => get_field( 'columns' ),
			'gutter'     => get_field( 'gutter' ),
			'image_size' => get_field( 'image_size' ),
			'template'   => get_field( 'template' ),
		);

		$loader = new Mai_Grid_Template_Loader;

		$args = array(
			'post_type'      => $data['content'],
			'posts_per_page' => $data['number'],
			'post_status'    => 'publish',
		);

		$posts = new WP_Query( $args );

		if ( $posts->have_posts() ):

			$items   = $posts->post_count;
			$columns = absint( $data['columns'] );
			$rows    = absint( ceil( $items / $columns ) );
			$empty   = ( $columns * $rows ) - $items;

			printf( '<div class="mai-grid mai-grid-%s" style="--columns: %s;--mai-grid-gutter: %s;--empty: %s;">',
				sanitize_html_class( $data['template'] ),
				$data['columns'],
				'16px',
				$empty
			);

				while ( $posts->have_posts() ) : $posts->the_post();

					$loader->set_template_data( $data );
					$loader->get_template_part( $data['template'] );

				endwhile;

			echo '</div>';

		endif;
		wp_reset_postdata();

		// $template->get_template_part( 'standard' );
		// echo $this->get_grid_entries( $block );
	}

	function get_templates() {
		return array(
			'standard' => array(
				'label'    => __( 'Standard', 'mai-grid' ),
				'defaults' => array(
					'gutter' => 'xl',
				),
				// 'disable' => array(),
			),
			'background' => array(
				'label' => __( 'Background', 'mai-grid' ),
				'defaults' => array(
					'gutter' => 'xs',
				),
			),
		);
	}

	function filters() {

		// $templates = $this->get_templates();

		// foreach( $this->field_names as $key => $name ) {

		// 	add_filter( "acf/load_field/key={$key}", function( $field ) {

		// 	});
		// }

		// Template.
		add_filter( 'acf/load_field/key=field_5de9b96fb69b0', array( $this, 'load_templates' ) );

		// Change conditionals based on registered template args.
		// add_filter( 'acf/load_field/key=field_5c8542d6a67c5', function( $field ) {

		// 	return $field;

		// 	// "Show if" logic.
		// 	$logic = array(
		// 		"field"    => "field_5de9b96fb69b0",
		// 		"operator" => "!=",
		// 		"value"    => "background",
		// 	);

		// 	// If existing conditions.
		// 	if ( $field['conditional_logic'] ) {
		// 		// Add this conditional to all condition groups.
		// 		foreach( $field['conditional_logic'] as $key => $values ) {
		// 			$field['conditional_logic'][ $key ][] = $logic;
		// 		}
		// 	}
		// 	// No existing conditions.
		// 	else {
		// 		// The only conditional.
		// 		$field['conditional_logic'][] = $logic;
		// 	}

		// 	return $field;
		// });

		/**
		 * Query.
		 */
		// Post Types.
		add_filter( 'acf/load_field/key=field_5bd3ea2224a92', array( $this, 'load_post_types' ) );
		// Get Entries By.
		add_filter( 'acf/load_field/key=field_5bd530b715aaf', array( $this, 'load_query_by' ) );
		// Posts.
		add_filter( 'acf/fields/post_object/query/key=field_5bd3ef320bc3a', array( $this, 'get_posts' ), 10, 1 );
		// Taxonomy.
		add_filter( 'acf/load_field/key=field_5bd51d7e47d05', array( $this, 'load_taxonomies' ) );
		// Terms.
		add_filter( 'acf/fields/taxonomy/query/key=field_5bd524aa95dbe', array( $this, 'get_terms' ), 10, 1 );
		// Parent.
		add_filter( 'acf/fields/post_object/query/key=field_5c85592ee744b', array( $this, 'get_parents' ), 10, 1 );

		// Exclude Content.
		add_filter( 'acf/load_field/key=field_5c853a6eff47d', array( $this, 'load_exclude_content' ) );

		// Order By.
		add_filter( 'acf/load_field/key=field_5c852d15e2507', array( $this, 'load_orderby' ) );
		// Order.
		add_filter( 'acf/load_field/key=field_5c852df4e2508', array( $this, 'load_order' ) );


		/**
		 * Display.
		 */
		// Show.
		add_filter( 'acf/load_field/key=field_5bd3eae824a93', array( $this, 'load_show' ) );
		// Image Size.
		add_filter( 'acf/load_field/key=field_5bd50e580d1e9', array( $this, 'load_image_sizes' ) );
		// Align Columns.
		add_filter( 'acf/load_field/key=field_5c853e6672972', array( $this, 'load_align_columns' ) );
		// Align Text.
		add_filter( 'acf/load_field/key=field_5c853f84eacd6', array( $this, 'load_align_text' ) );
		// Columns.
		add_filter( 'acf/load_field/key=field_5c854069d358c', array( $this, 'load_columns' ) );
		// Gutter.
		add_filter( 'acf/load_field/key=field_5c8542d6a67c5', array( $this, 'load_gutter' ) );
		// More Link Text.
		add_filter( 'acf/load_field/key=field_5c85465018395', array( $this, 'load_more_link_text' ) );

		/**
		 * Slider
		 */
		// Features.
		// add_filter( 'acf/load_field/key=field_5c8552d151d9a', array( $this, 'load_slider_features' ) );
		// Slides To Scroll.
		// add_filter( 'acf/load_field/key=field_5c85536f60107', array( $this, 'load_slider_speed' ) );
	}

	function load_templates( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			'standard'   => __( 'Standard', 'mai-grid' ),
			'background' => __( 'Background', 'mai-grid' ),
		);

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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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

	function get_parents( $args ) {
		if ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {
			$args['post_type'] = $_REQUEST['post_type'];
		}
		return $args;
	}

	function load_exclude_content( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			'ASC'  => __( 'Ascending', 'mai-grid' ),
			'DESC' => __( 'Descending', 'mai-grid' ),
		);

		return $field;
	}

	/**
	 * Load Show.
	 */
	function load_show( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

		$field['choices'] = array(
			'image'       => 'Image',
			'title'       => 'Title',
			'date'        => 'Date',
			'author'      => 'Author',
			'excerpt'     => 'Excerpt',
			'content'     => 'Content',
			'more_link'   => 'Read More link',
			'meta'        => 'Post Meta',
			// 'price'       => 'Price',
			// 'add_to_cart' => 'Add To Cart',
		);

		$field['default_value'] = array( 'image', 'title' );

		return $field;
	}

	/**
	 * Load image sizes.
	 * Much of the code take from genesis_get_image_sizes().
	 */
	function load_image_sizes( $field ) {
		global $_wp_additional_image_sizes;
		$sizes = $field['choices'] = array();
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
			$field['choices'][$index] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
		}
		$field['default_value'] = isset( $sizes['one-third'] ) ? 'one-third' : 'thumbnail';
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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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

	function load_gutter( $field ) {

		// Start empty.
		$field['choices'] = array();

		// Keep admin clean.
		if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
			return $field;
		}

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
		$base_url = MAI_GRID_PLUGIN_URL . 'assets';
		$base_dir = MAI_GRID_PLUGIN_DIR . 'assets';
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		$version  = defined( 'CHILD_THEME_VERSION' ) && CHILD_THEME_VERSION ? CHILD_THEME_VERSION : PARENT_THEME_VERSION;
		if ( is_admin() ) {
			wp_enqueue_script( 'mai-grid-entries-admin', "{$base_url}/js/mai-grid-admin{$suffix}.js", array(), $version . '.' . date ( 'njYHi', filemtime( "{$base_dir}/js/mai-grid-admin{$suffix}.js" ) ), true );
			wp_localize_script( 'mai-grid-entries-admin', 'maiGridVars', $this->get_templates() );
		}
		wp_enqueue_style( 'mai-grid', "{$base_url}/css/mai-grid-entries{$suffix}.css", array(), $version . '.' . date ( 'njYHi', filemtime( "{$base_dir}/css/mai-grid-entries{$suffix}.css" ) ) );
	}

}
