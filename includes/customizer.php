<?php

// add_action( 'genesis_before', function() {

// 	$settings = get_option( 'maiengine' );
// 	vd( $settings );

// });

add_filter( 'kirki_config', function( $config ) {
	$config['url_path'] = MAI_GRID_PLUGIN_URL . 'vendor/aristath/kirki';
	return $config;
});

add_action( 'init', 'maiengine_kirki_settings' );
function maiengine_kirki_settings() {

	// Bail if no Kirki.
	if ( ! class_exists( 'Kirki' ) ) {
		return;
	}

	// Settings config.
	$config = new Mai_Settings_Config( 'archive' );
	$fields = $config->get_fields();

	// IDs.
	$config_id = $panel_id = $settings_field = 'maiengine';

	/**
	 * Kirki Config.
	 */
	Kirki::add_config( $config_id, array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'option',
		'option_name' => $settings_field,
	) );

	$panel_id = 'maiengine_maitheme';

	/**
	 * Mai Theme.
	 */
	Kirki::add_panel( $panel_id, array(
		'title'       => esc_attr__( '!!!! Mai Theme', 'mai-engine' ),
		'description' => esc_attr__( 'Nice description.', 'mai-engine' ),
		'priority'    => 55,
	) );





	/********************************************************************
	 * Archives                                                         *
	 ********************************************************************/
	$section_id = 'maiengine_archives';

	Kirki::add_section( $section_id, [
		'title' => esc_attr__( 'Archives', 'mai-engine' ),
		'panel' => $panel_id,
	] );

	// Setup fields.
	foreach( $fields as $name => $field ) {

		// Bail if not an archive field.
		if ( ! $field['archive'] ) {
			continue;
		}

		// Skip if not string (temporary for testing/dev).
		// if ( ! is_string( $field['archive'] ) ) {
		// 	continue;
		// }

		Kirki::add_field( $config_id, $config->get_data( $name, $field, $section_id ) );
	}

	// Kirki::add_field( $config_id, [
	// 	'type'     => 'sortable',
	// 	'settings' => 'show',
	// 	'label'    => esc_html__( 'Show', 'mai-engine' ),
	// 	'section'  => $section_id,
	// 	'priority' => 10,
	// 	'choices'  => $config->get_choices( 'show' ),
	// 	'default'  => $fields['show']['default'],
	// ] );

	// Kirki::add_field( $config_id, [
	// 	'type'        => 'select',
	// 	'settings'    => 'image_orientation',
	// 	'label'       => esc_html__( 'This is the label', 'mai-engine' ),
	// 	'section'     => $section_id,
	// 	'default'     => 'landscape',
	// 	'priority'    => 10,
	// 	'multiple'    => 1,
	// 	'choices'  => $config->get_choices( 'image_orientation'  ),
	// 	'default'  => $fields['show']['default'],
	// ] );
}
