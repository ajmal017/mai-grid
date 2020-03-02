<?php

// add_action( 'genesis_before', function() {

// 	$settings = get_option( 'maiengine' );
// 	vd( $settings );

// });

add_filter( 'kirki_config', function( $config ) {
	$config['url_path'] = MAI_GRID_PLUGIN_URL . 'vendor/aristath/kirki';
	return $config;
});

// add_filter( 'kirki_config', function( $config ) {
// 	return wp_parse_args( array(
// 		'logo_image'   => 'https://kirki.org/images/logo.png',
// 		'description'  => esc_html__( 'The theme description.', 'kirki' ),
// 		'color_accent' => '#0091ea',
// 		'color_back'   => '#ffffff',
// 	), $config );
// });

add_action( 'customize_controls_enqueue_scripts', function() {
	mai_enqueue_asset( 'fields', 'css' );
});


function mai_kirki_config_id() {
	return 'maitheme';
}

add_action( 'init', 'mai_kirki_config' );
function mai_kirki_config() {

	$config_id = mai_kirki_config_id();

	/**
	 * Kirki Config.
	 */
	Kirki::add_config( $config_id, array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => $config_id,
	) );

	/**
	 * Mai Theme.
	 */
	Kirki::add_panel( $config_id, array(
		'title'       => esc_attr__( '!!!! Mai Theme', 'mai-engine' ),
		'description' => esc_attr__( 'Nice description.', 'mai-engine' ),
		'priority'    => 55,
	) );

}

add_action( 'init', 'mai_kirki_post_archive_settings' );
function mai_kirki_post_archive_settings() {

	$post_types = [ 'post' ];

	foreach( $post_types as $post_type ) {
		mai_add_archive_customizer_settings( $post_type, 'post_type' );
	}

}

add_action( 'init', 'mai_kirki_category_archive_settings' );
function mai_kirki_category_archive_settings() {
	mai_add_archive_customizer_settings( 'category', $type = 'taxonomy' );
}

/**
 *
 * @param  string  $name  The registered content type name.
 * @param  string  $type  The object type. Either 'taxonomy' or 'post_type'.
 */
function mai_add_archive_customizer_settings( $name, $type = 'post_type' ) {

	// Bail if no Kirki.
	if ( ! class_exists( 'Kirki' ) ) {
		return;
	}

	// Get label.
	switch ( $type ) {
		case 'post_type':
			$post_type = get_post_type_object( $name );
			$label     = $post_type->labels->singular_name;
			$label     = trim( $label . ' ' . esc_attr__( 'Archives', 'mai-engine' ) );
		break;
		case 'taxonomy':
			$taxonomy  = get_taxonomy( $name );
			$label     = $taxonomy->labels->singular_name;
			$label     = trim( $label . ' ' . esc_attr__( 'Archives', 'mai-engine' ) );
		break;
		case 'search':
			$label     = esc_attr__( 'Search Results', 'mai-engine' );
		break;
		case 'author':
			$label     = esc_attr__( 'Author Archives', 'mai-engine' );
		break;
		default:
			$label = '';
	}

	// Get data.
	$section_id = sprintf( '%s_archives', $name );
	$config_id  = mai_kirki_config_id();
	$settings   = new Mai_Entry_Settings( 'archive' );
	$fields     = $settings->get_fields();
	$prefix     = sprintf( '%s_', $name );

	// Section.
	Kirki::add_section( $section_id, [
		'title' => $label,
		'panel' => $config_id,
	] );

	// Loop through fields.
	foreach( $fields as $field_name => $field ) {

		// Bail if not an archive field.
		if ( ! $field['archive'] ) {
			continue;
		}

		// Add field.
		Kirki::add_field( $config_id, $settings->get_data( $field_name, $field, $section_id, $prefix ) );
	}

}
