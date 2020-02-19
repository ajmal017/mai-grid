<?php

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

	// Settings helper class.
	$helper = new Mai_Settings_Helper;

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
	 * Archives & Grid Blocks                                           *
	 ********************************************************************/
	$section_id = 'maiengine_archives';

	Kirki::add_section( $section_id, [
		'title' => esc_attr__( 'Archives', 'mai-engine' ),
		'panel' => $panel_id,
	] );

	Kirki::add_field( $config_id, [
		'type'     => 'sortable',
		'settings' => 'show',
		'label'    => esc_html__( 'Show', 'mai-engine' ),
		'section'  => $section_id,
		'priority' => 10,
		'choices'  => $helper->get_choices( 'show' ),
		'default'  => $helper->get_default( 'show' ),
	] );

	Kirki::add_field( $config_id, [
		'type'        => 'select',
		'settings'    => 'image_orientation',
		'label'       => esc_html__( 'This is the label', 'mai-engine' ),
		'section'     => $section_id,
		'default'     => 'landscape',
		'priority'    => 10,
		'multiple'    => 1,
		'choices'  => $helper->get_choices( 'image_orientation' ),
		'default'  => $helper->get_default( 'image_orientation' ),
	] );

}
