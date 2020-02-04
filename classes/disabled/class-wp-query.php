<?php

add_action( 'plugins_loaded', function() {
	new Mai_Grid_WP_Query;
});

class Mai_Grid_WP_Query {

	protected $args;

	function __construct() {
		$this->args = array();
		$this->load_fields();
		// $this->get_fields();
	}

	function load_fields() {
		add_filter( 'acf/load_field/key=field_5df1053632ca2', [ $this, 'load_post_types' ] );
	}

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

	function get_fields() {
		$this->post_type = get_field( 'post_type' );
	}

}
