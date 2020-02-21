<?php

class Mai_Settings_Config {

	// protected $fields;

	// function __construct() {
	// 	$this->fields = $this->get_fields();
	// }

	/**
	 * Get field configs.
	 */
	function get_fields() {
		return [
			/***********
			 * Display *
			 ***********/
			'show' => [
				'label'    => esc_html__( 'Show', 'mai-engine' ),
				'block'    => true,
				'archive'  => 'sortable',
				'singular' => true,
				'acf'      => 'field_5e441d93d6236',
				'default'  => [ 'image', 'title' ],
			],
			'image_orientation' => [
				'label'    => esc_html__( 'Image Orientation', 'mai-engine' ),
				'block'    => true,
				'archive'  => 'select',
				'singular' => true,
				'acf'      => 'field_5e4d4efe99279',
				'default'  => 'landscape',
				'conditions' => [
					[
						'setting'        => 'show',
						'acf_operator'   => '==',
						'kirki_operator' => 'contains',
						'value'          => 'image',
					],
				],
			],
			'image_size' => [
				'label'      => esc_html__( 'Image Size', 'mai-engine' ),
				'block'      => true,
				'archive'    => 'select',
				'singular'   => true,
				'acf'        => 'field_5bd50e580d1e9',
				'default'    => 'landscape-md',
				'conditions' => [
					[
						'setting'        => 'show',
						'acf_operator'   => '==',
						'kirki_operator' => 'contains',
						'value'          => 'image',
					],
					[
						'setting'  => 'image_orientation',
						'operator' => '==',
						'value'    => 'custom',
					],
				],
			],
			'image_position' => [
				'label'      => esc_html__( 'Image Position', 'mai-engine' ),
				'block'      => true,
				'archive'    => 'select',
				'singular'   => false,
				'acf'        => 'field_5e2f3adf82130',
				'default'    => '',
				'conditions' => [
					[
						'setting'        => 'show',
						'acf_operator'   => '==',
						'kirki_operator' => 'contains',
						'value'          => 'image',
					],
				],
			],
			'header_meta' => [
				'label'      => esc_html__( 'Header Meta', 'mai-engine' ),
				'block'      => true,
				'archive'    => 'text',
				'singular'   => true,
				'acf'        => 'field_5e2b563a7c6cf',
				'default'    => '',
				'conditions' => [
					[
						'setting'        => 'show',
						'acf_operator'   => '==',
						'kirki_operator' => 'contains',
						'value'          => 'header_meta',
					],
				],
			],
			'content_limit' => [
				'label'    => esc_html__( 'Content Limit', 'mai-engine' ),
				'desc'     => esc_html__( 'Limit the number of characters shown for the content or excerpt. Use 0 for no limit.', 'mai-engine' ),
				'block'    => true,
				'archive'  => 'text',
				'singular' => false,
				'acf'      => 'field_5bd51ac107244',
				'default'  => '',
				'conditions' => [
					[
						[
							'setting'        => 'show',
							'acf_operator'   => '==',
							'kirki_operator' => 'contains',
							'value'          => 'excerpt',
						],
					],
					[
						[
							'setting'        => 'show',
							'acf_operator'   => '==',
							'kirki_operator' => 'contains',
							'value'          => 'content',
						],
					],
				]
			],
			'more_link_text' => [
				'label'    => esc_html__( 'More Link Text', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => true,
				// TODO:      Filter on this default? Will we have a separate filter in v2?
				'acf'      => 'field_5c85465018395',
				'default'  => esc_html__( 'Read More', 'mai-engine' ),
			],
			'footer_meta' => [
				'label'    => esc_html__( 'Footer Meta', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => true,
				'acf'      => 'field_5e2b567e7c6d0',
				'default'  => '',
			],
			'boxed' => [
				'label'    => esc_html__( 'Boxed', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => true,
				'acf'      => 'field_5e2a08a182c2c',
				'default'  => '',
			],
			'align_text' => [
				'label'    => esc_html__( 'Align Text', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => true,
				'acf'      => 'field_5c853f84eacd6',
				'default'  => '',
			],
			'align_text_vertical' => [
				'label'    => esc_html__( 'Align Text (vertical)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => true,
				'acf'      => 'field_5e2f519edc912',
				'default'  => '',
			],
			/**********
			 * Layout *
			 **********/
			'columns_responsive' => [
				'label'    => esc_html__( 'Custom responsive columns', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5e334124b905d',
				'default'  => '',
			],
			'columns' => [
				'label'    => esc_html__( 'Columns (desktop)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5c854069d358c',
				'default'  => 3,
			],
			'columns_md' => [
				'label'    => esc_html__( 'Columns (lg tablets)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5e3305dff9d8b',
				'default'  => '',
			],
			'columns_sm' => [
				'label'    => esc_html__( 'Columns (sm tablets)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5e3305f1f9d8c',
				'default'  => '',
			],
			'columns_xs' => [
				'label'    => esc_html__( 'Columns (mobile)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5e332a5f7fe08',
				'default'  => '',
			],
			'align_columns' => [
				'label'    => esc_html__( 'Align Columns', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5c853e6672972',
				'default'  => '',
			],
			'align_columns_vertical' => [
				'label'    => esc_html__( 'Align Columns (vertical)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5e31d5f0e2867',
				'default'  => '',
			],
			'column_gap' => [
				'label'    => esc_html__( 'Column Gap', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5c8542d6a67c5',
				'default'  => '24px',
			],
			'row_gap' => [
				'label'    => esc_html__( 'Row Gap', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'acf'      => 'field_5e29f1785bcb6',
				'default'  => '24px',
			],
			/************
			 * WP_Query *
			 ************/
			'post_type' => [
				'label'    => esc_html__( 'Post Type', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632ca2',
				'default'  => array( 'post' ),
			],
			'query_by' => [
				'label'    => esc_html__( 'Get Entries By', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632cad',
				'default'  => 'date',
			],
			'number' => [
				'label'    => esc_html__( 'Number of Entries', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632ca8',
				'default'  => '12',
			],
			'offset' => [
				'label'    => esc_html__( 'Offset', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1bf01ea1de',
				'default'  => '',
			],
			'post__in' => [
				'label'    => esc_html__( 'Entries', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632cbc',
				'default'  => '',
			],
			'post__not_in' => [
				'label'    => esc_html__( 'Exclude Entries', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5e349237e1c01',
				'default'  => '',
			],
			'taxonomies' => [
				'label'    => esc_html__( 'Taxonomies', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1397316270',
				'default'  => '',
			],
			'taxonomy' => [
				'label'    => esc_html__( 'Taxonomy', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1398916271',
				'default'  => '',
			],
			'terms' => [
				'label'    => esc_html__( 'Terms', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df139a216272',
				'default'  => '',
			],
			'operator' => [
				'label'    => esc_html__( 'Operator', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df18f2305c2c',
				'default'  => 'IN',
			],
			'relation' => [
				'label'    => esc_html__( 'Relation', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df139281626f',
				'default'  => '',
			],
			'post_parent__in' => [
				'label'    => esc_html__( 'Parent', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632ce4',
				'default'  => '',
			],
			'orderby' => [
				'label'    => esc_html__( 'Order By', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632cec',
				'default'  => 'date',
			],
			'meta_key' => [
				'label'    => esc_html__( 'Meta key', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632cf4',
				'default'  => '',
			],
			'order' => [
				'label'    => esc_html__( 'Order', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632cfb',
				'default'  => '',
			],
			'exclude' => [
				'label'    => esc_html__( 'Exclude', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'acf'      => 'field_5df1053632d03',
				'default'  => '',
			],
			/*****************
			 * WP_Term_Query *
			 *****************/
		];
	}

	function get_choices( $field ) {
		return $this->$field();
	}
	/**
	 * TODO: On singular, add after_entry (widget_area), author_box, adjacent_entry_nav.
	 * TODO: On archive and singular, add genesis hooks.
	 */
	function show() {
		return [
			'image'       => esc_html__( 'Image', 'mai-engine' ),
			'title'       => esc_html__( 'Title', 'mai-engine' ),
			'header_meta' => esc_html__( 'Header Meta', 'mai-engine' ),
			'excerpt'     => esc_html__( 'Excerpt', 'mai-engine' ),
			'content'     => esc_html__( 'Content', 'mai-engine' ),
			'more_link'   => esc_html__( 'Read More link', 'mai-engine' ),
			'footer_meta' => esc_html__( 'Footer Meta', 'mai-engine' ),
		];
	}
	/**
	 * TODO: Conditionally check if image sizes are supported.
	 */
	function image_orientation() {
		return [
			'landscape' => esc_html__( 'Landscape', 'mai-engine' ),
			'portrait'  => esc_html__( 'Portrait', 'mai-engine' ),
			'square'    => esc_html__( 'Square', 'mai-engine' ),
			'custom'    => esc_html__( 'Custom', 'mai-engine' ),
		];
	}
	function image_size() {
		$choices = [];
		$sizes   = mai_get_available_image_sizes_new();
		foreach ( $sizes as $index => $value ) {
			$choices[ $index ] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
		}
		return $choices;
	}
	function image_position() {
		return [
			'full'       => esc_html__( 'Full', 'mai-engine' ),
			'left'       => esc_html__( 'Left', 'mai-engine' ),
			'center'     => esc_html__( 'Center', 'mai-engine' ),
			'right'      => esc_html__( 'Right', 'mai-engine' ),
			'background' => esc_html__( 'Background', 'mai-engine' ),
		];
	}
	function align_text() {
		return [
			''       => esc_html__( 'Clear', 'mai-engine' ),
			'start'  => esc_html__( 'Start', 'mai-engine' ),
			'center' => esc_html__( 'Center', 'mai-engine' ),
			'end'    => esc_html__( 'End', 'mai-engine' ),
		];
	}
	function align_text_vertical() {
		return [
			''       => esc_html__( 'Clear', 'mai-engine' ),
			'top'    => esc_html__( 'Top', 'mai-engine' ),
			'middle' => esc_html__( 'Middle', 'mai-engine' ),
			'bottom' => esc_html__( 'Bottom', 'mai-engine' ),
		];
	}
	function columns_responsive() {
		return $this->get_columns_choices( true );
	}
	function columns() {
		return $this->get_columns_choices();
	}
	function columns_md() {
		return $this->get_columns_choices();
	}
	function columns_sm() {
		return $this->get_columns_choices();
	}
	function columns_xs() {
		return $this->get_columns_choices();
	}
	function align_columns() {
		return [
			''       => esc_html__( 'Clear', 'mai-engine' ),
			'left'   => esc_html__( 'Left', 'mai-engine' ),
			'center' => esc_html__( 'Center', 'mai-engine' ),
			'right'  => esc_html__( 'Right', 'mai-engine' ),
		];
	}
	function align_columns_vertical() {
		return [
			''       => esc_html__( 'Clear', 'mai-engine' ),
			'top'    => esc_html__( 'Top', 'mai-engine' ),
			'middle' => esc_html__( 'Middle', 'mai-engine' ),
			'bottom' => esc_html__( 'Bottom', 'mai-engine' ),
		];
	}
	function post_type() {
		$choices    = [];
		$post_types = get_post_types( array(
			'public'             => true,
			'publicly_queryable' => true,
		), 'objects', 'or' );
		if ( $post_types ) {
			foreach ( $post_types as $name => $post_type ) {
				$choices[ $name ] = $post_type->label;
			}
		}
		return $choices;
	}
	function query_by() {
		return [
			'date'     => esc_html__( 'Date', 'mai-engine' ),
			'title'    => esc_html__( 'Title', 'mai-engine' ),
			'taxonomy' => esc_html__( 'Taxonomy', 'mai-engine' ),
			'parent'   => esc_html__( 'Parent', 'mai-engine' ),
		];
	}
	function taxonomies() {
		$choices = [];
		if ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) {
			$taxonomies = get_object_taxonomies( $_REQUEST['post_type'], 'objects' );
			if ( $taxonomies ) {
				foreach ( $taxonomies as $name => $taxo ) {
					$choices[ $name ] = $taxo->label;
				}
			}
		}
		return $choices;
	}
	function operator() {
		return [
			'IN'     => esc_html__( 'In', 'mai-engine' ),
			'NOT IN' => esc_html__( 'Not In', 'mai-engine' ),
		];
	}
	function post_parent__in() {
		$choices = [];
		if ( ! ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ) ) {
			return $choices;
		}
		$posts = acf_get_grouped_posts( array(
			'post_type'   => $_REQUEST['post_type'],
			'post_status' => 'publish',
		) );
		if ( ! $posts ) {
			return $choices;
		}
		$choices = $posts;
		return $choices;
	}
	function exclude() {
		return [
			'exclude_current'   => esc_html__( 'Exclude current', 'mai-engine' ),
			'exclude_displayed' => esc_html__( 'Exclude displayed', 'mai-engine' ),
		];
	}
	function orderby() {
		return [
			'title'          => esc_html__( 'Title', 'mai-engine' ),
			'name'           => esc_html__( 'Slug', 'mai-engine' ),
			'date'           => esc_html__( 'Date', 'mai-engine' ),
			'modified'       => esc_html__( 'Modified', 'mai-engine' ),
			'rand'           => esc_html__( 'Random', 'mai-engine' ),
			'comment_count'  => esc_html__( 'Comment Count', 'mai-engine' ),
			'menu_order'     => esc_html__( 'Menu Order', 'mai-engine' ),
			'post__in'       => esc_html__( 'Entries Order', 'mai-engine' ),
			'meta_value_num' => esc_html__( 'Meta Value Number', 'mai-engine' ),
		];
	}
	function order() {
		return [
			'ASC'  => esc_html__( 'Ascending', 'mai-engine' ),
			'DESC' => esc_html__( 'Descending', 'mai-engine' ),
		];
	}

	function get_columns_choices( $clear = false ) {
		$choices = [
			1 => esc_html__( '1', 'mai-engine' ),
			2 => esc_html__( '2', 'mai-engine' ),
			3 => esc_html__( '3', 'mai-engine' ),
			4 => esc_html__( '4', 'mai-engine' ),
			5 => esc_html__( '5', 'mai-engine' ),
			6 => esc_html__( '6', 'mai-engine' ),
			0 => esc_html__( 'Auto', 'mai-engine' ),
		];
		if ( $clear ) {
			$choices = array_merge( [ '' => esc_html__( 'Clear', 'mai-engine' ) ], $choices );
		}
		return $choices;
	}

}
