<?php

add_action( 'genesis_before', function() {

	$settings = get_option( 'maiengine' );
	vd( $settings );

});

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
	$config = new Mai_Settings_Config;
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
		if ( ! is_string( $field['archive'] ) ) {
			continue;
		}

		// Build Kirki data.
		$data = [
			'type'     => $field['archive'],
			'label'    => $field['label'],
			'default'  => $field['default'],
			'settings' => $name,
			'section'  => $section_id,
			'priority' => 10,
		];
		// Maybe add description.
		if ( isset( $field['desc'] ) ) {
			$data['description'] = $field['desc'];
		}
		// Maybe add choices.
		if ( method_exists( $config, $name ) ) {
			$data['choices'] = $config->get_choices( $name );
		}
		// Maybe add conditional logic.
		if ( isset( $field['conditions'] ) ) {
			$data['active_callback'] = mai_get_setting_conditions( 'kirki', $field );
		}

		Kirki::add_field( $config_id, $data );
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


/**
 * Get conditions for ACF from settings.
 * ACF uses field => {key} and kirki uses setting => {name}.
 * ACF uses == for checkbox, and kirki uses 'contains'.
 */
function mai_get_setting_conditions( $acf_or_kirki, $field ) {
	if ( is_array( $field['conditions'] ) ) {
		$count      = 0; // Kirki's nesting is different than ACF, so we need this.
		$conditions = [];
		foreach( $field['conditions'] as $index => $condition ) {
			// If 'AND' relation.
			if ( isset( $condition['setting'] ) ) {
				$conditions[] = mai_get_setting_condition( $acf_or_kirki, $condition, $field );
				$count++; // For Kirki's nesting.
			}
			// 'OR' relation.
			else {
				switch ( $acf_or_kirki ) {
					case 'acf':
						foreach( $condition as $child_condition ) {
							$conditions[ $index ][] = mai_get_setting_condition( $acf_or_kirki, $child_condition, $field );
						}
					break;
					case 'kirki':
						foreach( $condition as $child_condition ) {
							$conditions[ $count ][] = mai_get_setting_condition( $acf_or_kirki, $child_condition, $field );
						}
					break;
				}
			}
		}
		return $conditions;
	}
	return $field['conditions'];
}

function mai_get_setting_condition( $acf_or_kirki, $condition, $field ) {
	$array = [];
	switch ( $acf_or_kirki ) {
		case 'acf':
			$array = [
				'field'    => $field['acf'],
				'operator' => isset( $condition['acf_operator'] ) ? $condition['acf_operator'] : $condition['operator'],
				'value'    => $condition['value'],
			];
		break;
		case 'kirki':
			$array = [
				'setting'  => $condition['setting'],
				'operator' => isset( $condition['kirki_operator'] ) ? $condition['kirki_operator'] : $condition['operator'],
				'value'    => $condition['value'],
			];
		break;
	}
	return $array;
}
