<?php

/**
 *
 * @link              https://github.com/team-ok
 * @since             1.0.0
 * @package           Lablab_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       Lablab Builder
 * Plugin URI:        https://github.com/team-ok/lablab-builder
 * Description:       Lablab Builder is a modular WordPress page builder plugin that combines the flexibility of the Beans framework with the extendability of Advanced Custom Fields (ACF). 
 * Version:           1.0.0
 * Author:            Timo Klemm
 * Author URI:        https://github.com/team-ok
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lablab
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lablab-builder-activator.php
 */
function activate_lablab_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lablab-builder-activator.php';
	Lablab_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lablab-builder-deactivator.php
 */
function deactivate_lablab_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lablab-builder-deactivator.php';
	Lablab_Builder_Deactivator::deactivate();
}

// register_activation_hook( __FILE__, 'activate_lablab_builder' );
// register_deactivation_hook( __FILE__, 'deactivate_lablab_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lablab-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lablab_builder() {

	$plugin = new Lablab_Builder();
	$plugin->run();

}

run_lablab_builder();
