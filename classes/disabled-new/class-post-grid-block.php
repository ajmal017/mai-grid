<?php


class Mai_Post_Grid_Block extends Mai_Grid_Block {

	protected $post_filters_run;

	function __construct() {

		// Register the block.
		add_action( 'acf/init', array( $this, 'register_block' ), 10, 3 );

		// Run field group filters once.
		$this->post_filters_run = false;
		if ( is_admin() && ! $this->post_filters_run ) {
			$this->run_post_block_filters();
			$this->post_filters_run = true;
		}
	}

	function register_block() {
		// Bail if no ACF Pro >= 5.8.
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}
		// Register.
		acf_register_block_type( array(
			'name'            => 'mai-post-grid',
			'title'           => __( 'Mai Post Grid', 'mai-grid' ),
			'description'     => __( 'Display posts in various layouts.', 'mai-grid' ),
			'icon'            => 'grid-view',
			'category'        => 'widgets',
			'keywords'        => array( 'grid', 'post', 'entries' ),
			// 'mode'            => 'auto',
			// 'mode'            => 'edit',
			'mode'            => 'preview',
			'enqueue_assets'  => array( $this, 'enqueue_assets'),
			'render_callback' => array( $this, 'do_block' ),
			'supports'        => array(
				'align'  => array( 'wide' ),
				'ancher' => true,
			),
		) );
	}

	function do_block( $block, $content = '', $is_preview = false ) {

		$grid = new Mai_Post_Grid;
		echo $grid->get();
	}

	function run_post_block_filters() {
		// $this->values = array();
		// Post Types.
		add_filter( "acf/load_field/key={$this->fields['post_type']}",              array( $this, 'load_post_types' ) );
		// Get Entries By.
		add_filter( "acf/load_field/key={$this->fields['query_by']}",               array( $this, 'load_query_by' ) );
		// Entries.
		add_filter( "acf/fields/post_object/query/key={$this->fields['post__in']}", array( $this, 'get_posts' ), 10, 1 );
		// Taxonomy.
		add_filter( "acf/load_field/key={$this->fields['taxonomy']}",               array( $this, 'load_taxonomies' ) );
		// Terms.
		add_filter( "acf/fields/taxonomy/query/key={$this->fields['terms']}",       array( $this, 'get_terms' ), 10, 1 );
		// Operator.
		add_filter( "acf/load_field/key={$this->fields['operator']}",               array( $this, 'load_operators' ) );
		// Parent.
		add_filter( "acf/fields/post_object/query/key={$this->fields['parent']}",   array( $this, 'get_parents' ), 10, 1 );
		// Exclude Content.
		add_filter( "acf/load_field/key={$this->fields['exclude']}",                array( $this, 'load_exclude' ) );
		// Order By.
		add_filter( "acf/load_field/key={$this->fields['orderby']}",                array( $this, 'load_orderby' ) );
		// Order.
		add_filter( "acf/load_field/key={$this->fields['order']}",                  array( $this, 'load_order' ) );
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
}
