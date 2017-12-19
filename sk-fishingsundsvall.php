<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://cybercom.com
 * @since             1.0.0
 * @package           Sk_Fishingsundsvall
 *
 * @wordpress-plugin
 * Plugin Name:       Sundsvalls kommun - Fiske i Sundsvall
 * Plugin URI:        https://github.com/Sundsvallskommun/WP-Plugin-FiskeISundsvall
 * Description:       Catch report module for fiskeisundsvall.se
 * Version:           1.0.0
 * Author:            Daniel Pihlström
 * Author URI:        http://cybercom.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sk-fishingsundsvall
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sk-fishingsundsvall-activator.php
 */
function activate_sk_fishingsundsvall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sk-fishingsundsvall-activator.php';
	Sk_Fishingsundsvall_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sk-fishingsundsvall-deactivator.php
 */
function deactivate_sk_fishingsundsvall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sk-fishingsundsvall-deactivator.php';
	Sk_Fishingsundsvall_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sk_fishingsundsvall' );
register_deactivation_hook( __FILE__, 'deactivate_sk_fishingsundsvall' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sk-fishingsundsvall.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sk_fishingsundsvall() {

	$plugin = new Sk_Fishingsundsvall();
	$plugin->run();

}
run_sk_fishingsundsvall();
