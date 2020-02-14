<?php

/**
 * Plugin Name:     Mai Grid
 * Plugin URI:      https://maitheme.com
 * Description:     An easy to use block to display page/post/cpt/category/tag/term entries with customizeable layouts.
 * Version:         0.1.0
 *
 * Author:          BizBudding, Mike Hemberger
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Mai_Grid_Plugin Class.
 *
 * @since 0.1.0
 */
final class Mai_Grid_Plugin {

	/**
	 * @var   Mai_Grid_Plugin The one true Mai_Grid_Plugin
	 * @since 0.1.0
	 */
	private static $instance;

	/**
	 * Main Mai_Grid_Plugin Instance.
	 *
	 * Insures that only one instance of Mai_Grid_Plugin exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   0.1.0
	 * @static  var array $instance
	 * @uses    Mai_Grid_Plugin::setup_constants() Setup the constants needed.
	 * @uses    Mai_Grid_Plugin::includes() Include the required files.
	 * @uses    Mai_Grid_Plugin::hooks() Activate, deactivate, etc.
	 * @see     Mai_Grid_Plugin()
	 * @return  object | Mai_Grid_Plugin The one true Mai_Grid_Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_Grid_Plugin;
			// Methods.
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'textdomain' ), '1.0' );
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'textdomain' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'MAI_GRID_VERSION' ) ) {
			define( 'MAI_GRID_VERSION', '0.1.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_GRID_PLUGIN_DIR' ) ) {
			define( 'MAI_GRID_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MAI_GRID_PLUGIN_URL' ) ) {
			define( 'MAI_GRID_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Includes Path.
		if ( ! defined( 'MAI_GRID_INCLUDES_DIR' ) ) {
			define( 'MAI_GRID_INCLUDES_DIR', MAI_GRID_PLUGIN_DIR . 'includes/' );
		}

		// Plugin Classes Path.
		if ( ! defined( 'MAI_GRID_CLASSES_DIR' ) ) {
			define( 'MAI_GRID_CLASSES_DIR', MAI_GRID_PLUGIN_DIR . 'classes/' );
		}

		// Templates Path.
		if ( ! defined( 'MAI_GRID_TEMPLATES_DIR' ) ) {
			define( 'MAI_GRID_TEMPLATES_DIR', MAI_GRID_PLUGIN_DIR . 'templates/' );
		}

		// Templates URL.
		if ( ! defined( 'MAI_GRID_TEMPLATES_URL' ) ) {
			define( 'MAI_GRID_TEMPLATES_URL', MAI_GRID_PLUGIN_URL . 'templates/' );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_GRID_PLUGIN_FILE' ) ) {
			define( 'MAI_GRID_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'MAI_GRID_BASENAME' ) ) {
			define( 'MAI_GRID_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}

	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   0.1.0
	 * @return  void
	 */
	private function includes() {
		// Include vendor libraries.
		require_once __DIR__ . '/vendor/autoload.php';
		// Includes.
		foreach ( glob( MAI_GRID_INCLUDES_DIR . '*.php' ) as $file ) { include $file; }
		// Classes.
		// foreach ( glob( MAI_GRID_CLASSES_DIR . '*.php' ) as $file ) { include $file; }
		// include_once __DIR__ . '/classes/class-template-loader.php';
		include_once __DIR__ . '/classes/class-grid-base.php';
		include_once __DIR__ . '/classes/class-grid-blocks.php';
		// include_once __DIR__ . '/classes/class-grid.php';
		// include_once __DIR__ . '/classes/class-grid-block.php';
		// include_once __DIR__ . '/classes/class-post-grid.php';
		// include_once __DIR__ . '/classes/class-post-grid-block.php';
		// include_once __DIR__ . '/includes/acf-sortable-checkbox/acf-sortable-checkbox.php';
		// include_once __DIR__ . '/includes/acf-sortable-group/acf-sortable-group.php';
	}

	/**
	 * Run the hooks.
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public function hooks() {
		add_action( 'admin_init',             array( $this, 'updater' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'load_json' ) );
	}

	/**
	//  * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @uses    https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return  void
	 */
	public function updater() {

		// Bail if current user cannot manage plugins.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'Puc_v4_Factory' ) ) {
			return;
		}

		// Setup the updater.
		// $updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/maithemewp/mai-grid-entries/', __FILE__, 'mai-grid' );
	}

	// function register_admin_scripts() {
	// }

	// function register_assets() {
		// $base   = MAI_GRID_PLUGIN_URL . 'assets';
		// $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		// wp_register_script( 'mai-grid-entries-admin', "{$base}/js/mai-grid-admin{$suffix}.js", array(), MAI_GRID_VERSION, true );
		// wp_register_style( 'mai-grid', "{$base}/css/mai-grid-entries{$suffix}.css", array(), MAI_GRID_VERSION );
	// }

	/**
	 * Add path to load acf json files.
	 *
	 * @since   0.1.0
	 *
	 * @param   array  The existing acf-json paths.
	 *
	 * @return  array  The modified paths.
	 */
	function load_json( $paths ) {
		$paths[] = untrailingslashit( MAI_GRID_PLUGIN_DIR ) . '/acf-json';
		return $paths;
	}

}

/**
 * The main function for that returns Mai_Grid_Plugin
 *
 * The main function responsible for returning the one true Mai_Grid_Plugin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = mai_grid_plugin(); ?>
 *
 * @since 0.1.0
 *
 * @return object|Mai_Grid_Plugin The one true Mai_Grid_Plugin Instance.
 */
function mai_grid_plugin() {
	return Mai_Grid_Plugin::instance();
}

// Get Mai_Grid_Plugin Running.
mai_grid_plugin();
