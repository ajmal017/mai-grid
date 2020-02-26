<?php

class Mai_Settings_Config {

	public $acf;
	public $fields;
	public $field_groups;

	function __construct( $acf = true ) {
		$this->acf          = (bool) $acf;
		$this->fields       = $this->get_fields();
		$this->field_groups = [
			'mai_post_grid' => 'group_5de9b54440a2f',
			'mai_term_grid' => 'group_5de9b54440a2g',
		];
	}

	/**
	 * Get field configs.
	 */
	function get_fields() {
		return [
			/***********
			 * Display *
			 ***********/
			'display_tab' => [
				'label'    => esc_html__( 'Display', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'tab',
				'key'      => 'field_5bd51cac98282',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '',
			],
			 'show' => [
				'label'    => esc_html__( 'Show', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => true,
				'type'     => $this->acf ? 'checkbox': 'sortable',
				'key'      => 'field_5e441d93d6236',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => [ 'image', 'title' ],
				'acf'      => [
					'wrapper' => [
						'width' => '',
						'class' => 'mai-sortable',
						'id'    => '',
					],
				],
			],
			'image_orientation' => [
				'label'      => esc_html__( 'Image Orientation', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => true,
				'type'       => 'select',
				'key'        => 'field_5e4d4efe99279',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => 'landscape',
				'conditions' => [
					[
						'setting'  => 'show',
						'operator' => $this->acf ? '==': 'contains',
						'value'    => 'image',
					],
				],
			],
			'image_size' => [
				'label'      => esc_html__( 'Image Size', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => true,
				'type'       => 'select',
				'key'        => 'field_5bd50e580d1e9',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => 'landscape-md',
				'conditions' => [
					[
						'setting'  => 'show',
						'operator' => $this->acf ? '==': 'contains',
						'value'    => 'image',
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
				'archive'    => true,
				'singular'   => false,
				'type'       => 'select',
				'key'        => 'field_5e2f3adf82130',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'show',
						'operator' => $this->acf ? '==': 'contains',
						'value'    => 'image',
					],
				],
			],
			'header_meta' => [
				'label'      => esc_html__( 'Header Meta', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => true,
				'type'       => 'text',
				'key'        => 'field_5e2b563a7c6cf',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '[post_date][post_author]',
				'conditions' => [
					[
						'setting'  => 'show',
						'operator' => $this->acf ? '==': 'contains',
						'value'    => 'header_meta',
					],
				],
			],
			'content_limit' => [
				'label'      => esc_html__( 'Content Limit', 'mai-engine' ),
				'desc'       => esc_html__( 'Limit the number of characters shown for the content or excerpt. Use 0 for no limit.', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => false,
				'type'       => 'text',
				'key'        => 'field_5bd51ac107244',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => 0,
				'conditions' => [
					[
						[
							'setting'  => 'show',
							'operator' => $this->acf ? '==': 'contains',
							'value'    => 'excerpt',
						],
					],
					[
						[
							'setting'  => 'show',
							'operator' => $this->acf ? '==': 'contains',
							'value'    => 'content',
						],
					],
				]
			],
			'more_link_text' => [
				'label'       => esc_html__( 'More Link Text', 'mai-engine' ),
				'block'       => true,
				'archive'     => true,
				'singular'    => false,
				'type'        => 'text',
				'key'         => 'field_5c85465018395',
				'group'       => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				// TODO: This text should be filtered, same as the template that outputs it.
				'default'     => esc_html__( 'Read More', 'mai-engine' ),
				'conditions'  => [
					[
						'setting'  => 'show',
						'operator' => $this->acf ? '==': 'contains',
						'value'    => 'more_link',
					],
				],
				'acf' => [
					'placeholder' => esc_html__( 'Read More', 'mai-engine' ),
				]
			],
			'footer_meta' => [
				'label'      => esc_html__( 'Footer Meta', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => true,
				'type'       => 'text',
				'key'        => 'field_5e2b567e7c6d0',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '[post_categories]',
				'conditions' => [
					[
						'setting'  => 'show',
						'operator' => $this->acf ? '==': 'contains',
						'value'    => 'footer_meta',
					],
				],
			],
			'boxed' => [
				'label'    => esc_html__( 'Boxed', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => $this->acf ? 'true_false' : 'checkbox', // Could try 'switch' in Kirki.
				'key'      => 'field_5e2a08a182c2c',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => true, // ACF has 1,
				'acf'      => [
					'message' => __( 'Display boxed', 'mai-engine' ),
				]
			],
			'align_text' => [
				'label'    => esc_html__( 'Align Text', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => $this->acf ? 'button_group' : 'radio-buttonset',
				'key'      => 'field_5c853f84eacd6',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '',
			],
			'align_text_vertical' => [
				'label'    => esc_html__( 'Align Text (vertical)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => $this->acf ? 'button_group' : 'radio-buttonset',
				'key'      => 'field_5e2f519edc912',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '',
				// TODO: Conditions to hide unless image position is where need to use this setting.
			],
			/**********
			 * Layout *
			 **********/
			'layout_tab' => [
				'label'    => esc_html__( 'Layout', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'tab',
				'key'      => 'field_5c8549172e6c7',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '',
			],
			'columns' => [
				'label'    => esc_html__( 'Columns (desktop)', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => $this->acf ? 'button_group' : 'radio-buttonset',
				'key'      => 'field_5c854069d358c',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => 3,
			],
			'columns_responsive' => [
				'label'    => ! $this->acf ? esc_html__( 'Custom responsive columns', 'mai-engine' ) : '',
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => $this->acf ? 'true_false' : 'checkbox', // Could try 'switch' in Kirki.
				'key'      => 'field_5e334124b905d',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '',
				'acf'      => [
					'message' => esc_html__( 'Custom responsive columns', 'mai-engine' ),
				]
			],
			'columns_md' => [
				'label'      => esc_html__( 'Columns (lg tablets)', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => false,
				'type'       => $this->acf ? 'button_group': 'radio-buttonset',
				'key'        => 'field_5e3305dff9d8b',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'columns_responsive',
						'operator' => '==',
						'value'    => 1,
					],
				],
			],
			'columns_sm' => [
				'label'      => esc_html__( 'Columns (sm tablets)', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => false,
				'type'       => $this->acf ? 'button_group': 'radio-buttonset',
				'key'        => 'field_5e3305f1f9d8c',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'columns_responsive',
						'operator' => '==',
						'value'    => 1,
					],
				],
			],
			'columns_xs' => [
				'label'      => esc_html__( 'Columns (mobile)', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => false,
				'type'       => $this->acf ? 'button_group': 'radio-buttonset',
				'key'        => 'field_5e332a5f7fe08',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'columns_responsive',
						'operator' => '==',
						'value'    => 1,
					],
				],
			],
			'align_columns' => [
				'label'      => esc_html__( 'Align Columns', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => false,
				'type'       => $this->acf ? 'button_group': 'radio-buttonset',
				'key'        => 'field_5c853e6672972',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'columns',
						'operator' => '!=',
						'value'    => 1,
					],
				],
			],
			'align_columns_vertical' => [
				'label'      => esc_html__( 'Align Columns (vertical)', 'mai-engine' ),
				'block'      => true,
				'archive'    => true,
				'singular'   => false,
				'type'       => $this->acf ? 'button_group': 'radio-buttonset',
				'key'        => 'field_5e31d5f0e2867',
				'group'      => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'    => 'columns',
						'operator' => '!=',
						'value'    => 1,
					],
				],
			],
			'column_gap' => [
				'label'    => esc_html__( 'Column Gap', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => 'text',
				'key'      => 'field_5c8542d6a67c5',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '24px',
			],
			'row_gap' => [
				'label'    => esc_html__( 'Row Gap', 'mai-engine' ),
				'block'    => true,
				'archive'  => true,
				'singular' => false,
				'type'     => 'text',
				'key'      => 'field_5e29f1785bcb6',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '24px',
			],
			/***********
			 * Entries *
			 ***********/
			'entries_tab' => [
				'label'    => esc_html__( 'Entries', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'tab',
				'key'      => 'field_5df13446c49cf',
				'group'    => [ 'mai_post_grid', 'mai_term_grid', 'mai_user_grid' ],
				'default'  => '',
			],
			/************
			 * WP_Query *
			 ************/
			'post_type' => [
				'label'    => esc_html__( 'Post Type', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'select',
				'key'      => 'field_5df1053632ca2',
				'group'    => [ 'mai_post_grid' ],
				'default'  => [ 'post' ],
				'acf'      => [
					'multiple' => 1,
					'ui'       => 1,
					'ajax'     => 1,
				]
			],
			'query_by' => [
				'label'    => esc_html__( 'Get Entries By', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'button_group',
				'key'      => 'field_5df1053632cad',
				'group'    => [ 'mai_post_grid' ],
				'default'  => 'date',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
				],
			],
			'number' => [
				'label'      => esc_html__( 'Number of Entries', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'number',
				'key'        => 'field_5df1053632ca8',
				'group'      => [ 'mai_post_grid' ],
				'default'    => 12,
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '!=',
						'value'    => 'title',
					],
				],
				'acf' => [
					'min' => 0,
				],
			],
			'offset' => [
				'label'      => esc_html__( 'Offset', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'number',
				'key'        => 'field_5df1bf01ea1de',
				'group'      => [ 'mai_post_grid' ],
				'default'    => 0,
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '!=',
						'value'    => 'title',
					],
				],
				'acf' => [
					'min' => 0,
				],
			],
			'post__in' => [
				'label'      => esc_html__( 'Entries', 'mai-engine' ),
				'desc'       => esc_html__( 'Show specific entries. Choose all that apply. If empty, Grid will get entries by date.', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'post_object',
				'key'        => 'field_5df1053632cbc',
				'group'      => [ 'mai_post_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '==',
						'value'    => 'title',
					],
				],
				'acf' => [
					'multiple'      => 1,
					'return_format' => 'id',
					'ui'            => 1,
				],
			],
			'post__not_in' => [
				'label'      => esc_html__( 'Exclude Entries', 'mai-engine' ),
				'desc'       => esc_html__( 'Hide specific entries. Choose all that apply.', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'post_object',
				'key'        => 'field_5e349237e1c01',
				'group'      => [ 'mai_post_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '==',
						'value'    => 'title',
					],
				],
				'acf' => [
					'multiple'      => 1,
					'return_format' => 'id',
					'ui'            => 1,
				],
			],
			'taxonomies' => [
				'label'    => esc_html__( 'Taxonomies', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'repeater',
				'key'      => 'field_5df1397316270',
				'group'    => [ 'mai_post_grid' ],
				'default'  => '',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '==',
						'value'    => 'taxonomy',
					],
				],
				'acf' => [
					'collapsed'    => 'field_5df1398916271',
					'min'          => 1,
					'max'          => 0,
					'layout'       => 'block',
					'button_label' => esc_html__( 'Add Taxonomy', 'mai-engine' ),
					'sub_fields'   => [
						'taxonomy' => [
							'key'     => 'field_5df1398916271',
							'label'   => esc_html__( 'Taxonomy', 'mai-engine' ),
							'type'    => 'select',
							'default' => [],
							'acf'     => [
								'ui'   => 1,
								'ajax' => 1,
							],
						],
						'terms' => [
							'key'      => 'field_5df139a216272',
							'label'    => esc_html__( 'Terms', 'mai-engine' ),
							'type'     => 'taxonomy',
							'default'  => [],
							'acf'      => [
								'field_type' => 'multi_select',
								'taxonomy'   => 'category',
								'add_term'   => 0,
								'save_terms' => 0,
								'load_terms' => 0,
								'multiple'   => 0,
							]
						],
						'operator' => [
							'key'     => 'field_5df18f2305c2c',
							'label'   => esc_html__( 'Operator', 'mai-engine' ),
							'type'    => 'select',
							'default' => [],
						],
					],
				]
			],
			'taxonomies_relation' => [
				'label'      => esc_html__( 'Relation', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'select',
				'key'        => 'field_5df139281626f',
				'group'      => [ 'mai_post_grid' ],
				'default'    => 'AND',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '==',
						'value'    => 'taxonomy',
					],
				],
			],
			'post_parent__in' => [
				'label'    => esc_html__( 'Parent', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'post_object',
				'key'      => 'field_5df1053632ce4',
				'group'    => [ 'mai_post_grid' ],
				'default'  => '',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
					[
						'setting'  => 'query_by',
						'operator' => '==',
						'value'    => 'parent',
					],
				],
				'acf' => [
					'multiple' => 1,
					'ui'       => 1,
					'ajax'     => 1,
				],
			],
			'orderby' => [
				'label'      => esc_html__( 'Order By', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'select',
				'key'        => 'field_5df1053632cec',
				'group'      => [ 'mai_post_grid' ],
				'default'    => 'date',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
				],
				'acf' => [
					'ui'   => 1,
					'ajax' => 1,
				],
			],
			// TODO: meta_query, like tax_query. Allow both?
			// 'meta_key' => [
			// 	'label'    => esc_html__( 'Meta key', 'mai-engine' ),
			// 	'block'    => true,
			// 	'archive'  => false,
			// 	'singular' => false,
			// 	'type'     => 'text',
			// 	'key'      => 'field_5df1053632cf4',
			// 	'group'    => [ 'mai_post_grid' ],
			// 	'default'  => '',
			// 	'conditions' => [
			// 		[
			// 			'setting'  => 'post_type',
			// 			'operator' => '!=empty',
			// 		],
			// 		[
			// 			'setting'  => 'orderby',
			// 			'operator' => '==',
			// 			'value'    => 'meta_value_num',
			// 		],
			// 	],
			// ],
			'order' => [
				'label'      => esc_html__( 'Order', 'mai-engine' ),
				'block'      => true,
				'archive'    => false,
				'singular'   => false,
				'type'       => 'select',
				'key'        => 'field_5df1053632cfb',
				'group'      => [ 'mai_post_grid' ],
				'default'    => '',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
				],
			],
			'exclude' => [
				'label'    => esc_html__( 'Exclude', 'mai-engine' ),
				'block'    => true,
				'archive'  => false,
				'singular' => false,
				'type'     => 'checkbox',
				'key'      => 'field_5df1053632d03',
				'group'    => [ 'mai_post_grid' ],
				'default'  => '',
				'conditions' => [
					[
						'setting'  => 'post_type',
						'operator' => '!=empty',
					],
				],
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
	// function columns_responsive() {
	// 	return $this->get_columns_choices( true );
	// }
	function columns() {
		return $this->get_columns_choices();
	}
	function columns_md() {
		return $this->get_columns_choices( true );
	}
	function columns_sm() {
		return $this->get_columns_choices( true );
	}
	function columns_xs() {
		return $this->get_columns_choices( true );
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

	function get_data( $name, $field, $section_id = '' ) {

		// If ACF field.
		if ( $this->acf ) {

			// Build ACF data.
			$data = [
				'name'  => $name,
				'key'   => $field['key'],
				'label' => $field['label'],
				'type'  => $field['type'],
			];
			// Maybe add description.
			if ( isset( $field['desc'] ) ) {
				$data['instructions'] = $field['desc'];
			}
			// ACF-specific fields.
			if ( isset( $field['acf'] ) ) {
				foreach( $field['acf'] as $key => $value ) {
					// Sub fields.
					if ( 'sub_fields' === $key ) {
						$data['sub_fields'] = [];
						foreach( $value as $sub_key => $sub_value ) {
							// $data['sub_fields'][] = $this->get_data( $sub_key, $field );
						}
					}
					// Standard field data.
					else {
						$data[ $key ] = $value;
					}
				}
			}
			// Maybe add conditional logic.
			if ( isset( $field['conditions'] ) ) {
				$data['conditional_logic'] = $this->get_conditions( $field );
			}
		}
		// Kirki.
		else {

			// Build Kirki data.
			$data = [
				'type'     => $field['type'],
				'label'    => $field['label'],
				'settings' => $name,
				'section'  => $section_id,
				'priority' => 10,
			];
			// Maybe add description.
			if ( isset( $field['desc'] ) ) {
				$data['description'] = $field['desc'];
			}
			// Kikri-specific fields.
			if ( isset( $field['kirki'] ) ) {
				foreach( $field['kirki'] as $key => $value ) {
					$data[ $key ] = $value;
				}
			}
			// Maybe add conditional logic.
			if ( isset( $field['conditions'] ) ) {
				$data['active_callback'] = $this->get_conditions( $field );
			}
		}

		// Maybe add default.
		if ( isset( $field['default'] ) ) {
			$data['default'] = $field['default'];
		}

		// TODO: Handle message/description for checkbox field (Boxed).

		return $data;
	}

	/**
	 * Get conditions for ACF from settings.
	 * ACF uses field => {key} and kirki uses setting => {name}.
	 * ACF uses == for checkbox, and kirki uses 'contains'.
	 */
	function get_conditions( $field ) {
		if ( is_array( $field['conditions'] ) ) {
			$count      = 0; // Kirki's nesting is different than ACF, so we need this.
			$conditions = [];
			foreach( $field['conditions'] as $index => $condition ) {
				// If 'AND' relation.
				if ( isset( $condition['setting'] ) ) {
					$conditions[] = $this->get_condition( $condition, $field );
					$count++; // For Kirki's nesting.
				}
				// 'OR' relation - nested one level further.
				else {
					if ( $this->acf ) {
						foreach( $condition as $child_condition ) {
							$conditions[ $index ][] = $this->get_condition( $child_condition, $field );
						}
					} else {
						foreach( $condition as $child_condition ) {
							$conditions[ $count ][] = $this->get_condition( $child_condition, $field );
						}
					}
				}
			}
			return $conditions;
		}
		return $field['conditions'];
	}

	function get_condition( $condition, $field ) {
		$array = [];
		if ( $this->acf ) {
			$array = [
				'field'    => $this->fields[ $condition['setting'] ]['key'],
				'operator' => $condition['operator'],
			];
			// ACF doesn't have a value for operators like '!=empty'.
			if ( isset( $condition['value'] ) ) {
				$array['value'] = $condition['value'];
			}
		} else {
			$array = [
				'setting'  => $condition['setting'],
				'operator' => $condition['operator'],
				'value'    => $condition['value'],
			];
		}
		return $array;
	}

}
