<?php

/**
 * Get Mai_Grid_Blocks Running.
 *
 * @since   0.1.0
 * @return  object
 */
// add_action( 'plugins_loaded', function() {
// 	return Mai_Grid_Blocks::instance();
// });

Mai_Grid_Blocks::instance();

final class Mai_Grid_Blocks  {

	/**
	 * @var    Mai_Grid_Blocks The one true Mai_Grid_Blocks
	 * @since  0.1.0
	 */
	private static $instance;

	private $templates;
	private $fields;

	/**
	 * Main Mai_Grid_Blocks Instance.
	 *
	 * Insures that only one instance of Mai_Grid_Blocks exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @return  object | Mai_Grid_Blocks The one true Mai_Grid_Blocks
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_Grid_Blocks;
			// Methods.
			self::$instance->run();
			self::$instance->run_filters();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-grid' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-grid' ), '1.0' );
	}

	function run() {
		$this->templates = Mai_Grid_Base::get_templates();
		$this->fields    = Mai_Grid_Base::get_fields();
		add_action( 'acf/init', array( $this, 'register_blocks' ), 10, 3 );
	}

	function register_blocks() {
		// Bail if no ACF Pro >= 5.8.
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}
		// Mai Post Grid.
		acf_register_block_type( array(
			'name'            => 'mai-post-grid',
			'title'           => __( 'Mai Post Grid', 'mai-grid' ),
			'description'     => __( 'Display posts/pages/cpts in various layouts.', 'mai-grid' ),
			'icon'            => 'grid-view',
			'category'        => 'widgets',
			'keywords'        => array( 'grid', 'post', 'page' ),
			// 'mode'            => 'auto',
			// 'mode'            => 'edit',
			'mode'            => 'preview',
			// 'enqueue_assets'  => array( $this, 'enqueue_assets'),
			'render_callback' => array( $this, 'do_post_grid' ),
			'supports'        => array(
				'align'  => array( 'wide' ),
				'ancher' => true,
			),
		) );
		// Mai Term Grid.
		acf_register_block_type( array(
			'name'            => 'mai-term-grid',
			'title'           => __( 'Mai Term Grid', 'mai-grid' ),
			'description'     => __( 'Display posts/pages/cpts in various layouts.', 'mai-grid' ),
			'icon'            => 'grid-view',
			'category'        => 'widgets',
			'keywords'        => array( 'grid', 'category', 'term' ),
			// 'mode'            => 'auto',
			// 'mode'            => 'edit',
			'mode'            => 'preview',
			// 'enqueue_assets'  => array( $this, 'enqueue_assets'),
			'render_callback' => array( $this, 'do_term_grid' ),
			'supports'        => array(
				'align'  => array( 'wide' ),
				'ancher' => true,
			),
		) );
	}

	function do_post_grid( $block, $content = '', $is_preview = false ) {
		$args = $this->get_base_feilds();
		$args = array_merge( $args, $this->get_wp_query_fields() );
		if ( ! empty( $block['className'] ) ) {
			$args['class'] = ( isset( $args['class'] ) && ! empty( $args['class'] ) ) ? ' ' . $block['className'] : $block['className'];
		}
		$grid = new Mai_Grid_Base( 'post', $args );
		echo $grid->get();
	}

	function do_term_grid( $block, $content = '', $is_preview = false ) {
		// TODO: block id?
		$args = $this->get_base_feilds();
		$args = array_merge( $args, $this->get_wp_term_query_args() );
		if ( ! empty( $block['className'] ) ) {
			$args['class'] = ( isset( $args['class'] ) && ! empty( $args['class'] ) ) ? ' ' . $block['className'] : $block['className'];
		}
		$grid = new Mai_Grid_Base( 'term', $args );
		echo $grid->get();
	}

	function get_base_feilds() {
		$args = array();
		foreach( Mai_Grid_Base::get_display_fields() as $name => $key ) {
			$args[ $name ] = $this->get_field( $name );
		}
		foreach( Mai_Grid_Base::get_layout_fields() as $name => $key ) {
			$args[ $name ] = $this->get_field( $name );
		}
		return $args;
	}

	function get_wp_query_fields() {
		$args = array();
		foreach( Mai_Grid_Base::get_wp_query_fields() as $name => $key ) {
			$args[ $name ] = $this->get_field( $name );
		}
		return $args;
	}

	function get_wp_term_query_args() {
		$args = array();
		foreach( Mai_Grid_Base::get_layout_fields() as $name => $key ) {
			$args[ $name ] = $this->get_field( $name );
		}
		return $args;
	}

	function get_field( $name ) {
		$value = get_field( $name );
		return is_null( $value ) ? $this->fields[ $name ]['default'] : $value;
	}

	function run_filters() {

		// Add field wrapper classes.
		add_filter( 'acf/field_wrapper_attributes', function( $wrapper, $field ) {
			// Show.
			if ( in_array( $field['key'], array(
				$this->fields['show_image']['key'],
				$this->fields['show_title']['key'],
				$this->fields['show_header_meta']['key'],
				$this->fields['show_excerpt']['key'],
				$this->fields['show_content']['key'],
				$this->fields['show_more_link']['key'],
				$this->fields['show_footer_meta']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-show' : 'mai-grid-show';
			}
			// Conditional Show.
			if ( in_array( $field['key'], array(
				$this->fields['image_size']['key'],
				$this->fields['image_align']['key'],
				$this->fields['header_meta']['key'],
				$this->fields['content_limit']['key'],
				$this->fields['more_link_text']['key'],
				$this->fields['footer_meta']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-show-conditional' : 'mai-grid-show-conditional';
			}
			// Button Group.
			if ( in_array( $field['key'], array(
				$this->fields['align_text']['key'],
				$this->fields['align_text_vertical']['key'],
				$this->fields['columns']['key'],
				$this->fields['columns_md']['key'],
				$this->fields['columns_sm']['key'],
				$this->fields['columns_xs']['key'],
				$this->fields['align_columns']['key'],
				$this->fields['align_columns_vertical']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-button-group' : 'mai-grid-button-group';
			}
			// Button Group.
			if ( in_array( $field['key'], array(
				$this->fields['align_text']['key'],
				$this->fields['align_text_vertical']['key'],
				$this->fields['columns_md']['key'],
				$this->fields['columns_sm']['key'],
				$this->fields['columns_xs']['key'],
				$this->fields['align_columns']['key'],
				$this->fields['align_columns_vertical']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-button-group-clear' : 'mai-grid-button-group-clear';
			}
			// Nested Columns.
			if ( in_array( $field['key'], array(
				$this->fields['columns_md']['key'],
				$this->fields['columns_sm']['key'],
				$this->fields['columns_xs']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-nested-columns' : 'mai-grid-nested-columns';
			}
			// Nested Columns First.
			if ( in_array( $field['key'], array(
				$this->fields['columns_md']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-nested-columns-first' : 'mai-grid-nested-columns-first';
			}
			// Nested Columns Last.
			if ( in_array( $field['key'], array(
				$this->fields['columns_xs']['key'],
			) ) ) {
				$wrapper['class'] = isset( $wrapper['class'] ) && ! empty( $wrapper['class'] ) ? $wrapper['class'] . ' mai-grid-nested-columns-last' : 'mai-grid-nested-columns-last';
			}
			return $wrapper;
		}, 10, 2 );

		/**
		 * WP_Query.
		 */
		// Post Types.
		add_filter( "acf/load_field/key={$this->fields['post_type']['key']}",                     array( $this, 'load_post_types' ) );
		// Get Entries By.
		add_filter( "acf/load_field/key={$this->fields['query_by']['key']}",                      array( $this, 'load_query_by' ) );
		// Entries.
		add_filter( "acf/fields/post_object/query/key={$this->fields['post__in']['key']}",        array( $this, 'get_posts' ), 10, 1 );
		// Taxonomy.
		add_filter( "acf/load_field/key={$this->fields['taxonomy']['key']}",                      array( $this, 'load_taxonomies' ) );
		// Terms.
		add_filter( "acf/fields/taxonomy/query/key={$this->fields['terms']['key']}",              array( $this, 'get_terms' ), 10, 1 );
		// Operator.
		add_filter( "acf/load_field/key={$this->fields['operator']['key']}",                      array( $this, 'load_operators' ) );
		// Parent.
		add_filter( "acf/fields/post_object/query/key={$this->fields['post_parent__in']['key']}", array( $this, 'get_parents' ), 10, 1 );
		// Exclude Content.
		add_filter( "acf/load_field/key={$this->fields['exclude']['key']}",                       array( $this, 'load_exclude' ) );
		// Order By.
		add_filter( "acf/load_field/key={$this->fields['orderby']['key']}",                       array( $this, 'load_orderby' ) );
		// Order.
		add_filter( "acf/load_field/key={$this->fields['order']['key']}",                         array( $this, 'load_order' ) );

		/**
		 * Display.
		 */
		// Template.
		add_filter( "acf/load_field/key={$this->fields['template']['key']}",                      array( $this, 'load_templates' ) );
		// Image Size.
		add_filter( "acf/load_field/key={$this->fields['image_size']['key']}",                    array( $this, 'load_image_sizes' ) );
		// Image Alignment.
		add_filter( "acf/load_field/key={$this->fields['image_align']['key']}",                   array( $this, 'load_image_align' ) );
		// More Link Text.
		add_filter( "acf/load_field/key={$this->fields['more_link_text']['key']}",                array( $this, 'load_more_link_text' ) );

		/**
		 * Layout.
		 */
		// Columns.
		add_filter( "acf/load_field/key={$this->fields['columns']['key']}",                       array( $this, 'load_columns' ) );
		add_filter( "acf/load_field/key={$this->fields['columns_md']['key']}",                    array( $this, 'load_columns_responsive' ) );
		add_filter( "acf/load_field/key={$this->fields['columns_sm']['key']}",                    array( $this, 'load_columns_responsive' ) );
		add_filter( "acf/load_field/key={$this->fields['columns_xs']['key']}",                    array( $this, 'load_columns_responsive' ) );
		// Align Columns.
		add_filter( "acf/load_field/key={$this->fields['align_columns']['key']}",                 array( $this, 'load_align_columns' ) );
		add_filter( "acf/load_field/key={$this->fields['align_columns_vertical']['key']}",        array( $this, 'load_align_columns_vertical' ) );
		// Align Text.
		add_filter( "acf/load_field/key={$this->fields['align_text']['key']}",                    array( $this, 'load_align_text' ) );
		add_filter( "acf/load_field/key={$this->fields['align_text_vertical']['key']}",           array( $this, 'load_align_text_vertical' ) );

		// Labels.
		foreach( Mai_Grid_Base::get_display_fields() as $name => $values ) {
			// Skip if name does not contain 'show_'.
			if ( false === strpos ( $name, 'show_' ) ) {
				continue;
			}
			add_filter( "acf/load_field/key={$values['key']}", function( $field ) {
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
		foreach( Mai_Grid_Base::get_display_fields() as $name => $values ) {
			// Skip template field.
			if ( 'template' === $name ) {
				continue;
			}
			// Skip show field.
			if ( 'show' === $name ) {
				continue;
			}
			// Skip show field.
			if ( 'show_items' === $name ) {
				continue;
			}
			// Add filter.
			add_filter( "acf/load_field/key={$values['key']}", function( $field ) {
				// Keep admin clean.
				if ( is_admin() && ( 'acf-field-group' === get_post_type() ) ) {
					return $field;
				}
				$field = $this->add_conditional_logic( $field );
				return $field;
			});
		}
		// Defaults.
		foreach( $this->fields as $name => $values ) {
			// Skip template field.
			if ( 'template' === $name ) {
				continue;
			}
			// Add filter.
			add_filter( "acf/load_field/key={$values['key']}", function( $field ) {
				// Set default from our config filter.
				$field['default'] =$this->fields[ $field['name'] ]['default'];
				return $field;
			});
		}
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

		// TODO: Handle actual exclusion.
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
			'start'  => __( 'Start', 'mai-grid' ),
			'center' => __( 'Center', 'mai-grid' ),
			'end'    => __( 'End', 'mai-grid' ),
		);

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
				'field'    => $this->fields['template']['key'],
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
