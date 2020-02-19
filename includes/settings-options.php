<?php

function mai_get_settings_fields() {

	static $fields = [];

	if ( isset( $fields ) & ! empty( $fields ) ) {
		return $fields;
	}

	return [
		'show' => [
			'acf'     => 'field_5e441d93d6236',
			'default' => [ 'image', 'title' ],
			'choices' => [
				'image'       => __( 'Image', 'mai-engine' ),
				'title'       => __( 'Title', 'mai-engine' ),
				'header_meta' => __( 'Header Meta', 'mai-engine' ),
				'excerpt'     => __( 'Excerpt', 'mai-engine' ),
				'content'     => __( 'Content', 'mai-engine' ),
				'more_link'   => __( 'Read More link', 'mai-engine' ),
				'footer_meta' => __( 'Footer Meta', 'mai-engine' ),
			],
		],
		/**
		 * TODO: Conditionally check if image sizes are supported.
		 */
		'image_orientation' => [
			'acf'     => 'field_5e4d4efe99279',
			'default' => 'landscape',
			'choices' => [
				'landscape' => __( 'Landscape', 'mai-engine' ),
				'portrait'  => __( 'Portrait', 'mai-engine' ),
				'square'    => __( 'Square', 'mai-engine' ),
				'custom'    => __( 'Custom', 'mai-engine' ),
			],
		],

		'image_size' => [
			'acf'     => 'field_5bd50e580d1e9',
			'default' => 'landscape-md',
			'choices' => mai_get_image_sizes_choices(),
		],
		'image_position' => [
			'default' => '',
			'key'     => 'field_5e2f3adf82130',
		],
		'header_meta' => [
			'default' => '',
			'key'     => 'field_5e2b563a7c6cf',
		],
		'content_limit' => [
			'default' => '',
			'key'     => 'field_5bd51ac107244',
		],
		'more_link_text' => [
			// TODO: Filter on this default? Will we have a separate filter in v2?
			'default' => __( 'Read More', 'mai-grid' ),
			'key'     => 'field_5c85465018395',
		],
		'footer_meta' => [
			'default' => '',
			'key'     => 'field_5e2b567e7c6d0',
		],
		'boxed' => [
			'default' => '',
			'key'     => 'field_5e2a08a182c2c',
		],
		'align_text' => [
			'default' => '',
			'key'     => 'field_5c853f84eacd6',
		],
		'align_text_vertical' => [
			'default' => '',
			'key'     => 'field_5e2f519edc912',
		],


	];

}

function mai_get_image_size_choices() {
	global $_wp_additional_image_sizes;
	$choices = $sizes = [];
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
		$choices[ $index ] = sprintf( '%s (%s x %s)', $index, $value['width'], $value['height'] );
	}
	return $choices;
}
