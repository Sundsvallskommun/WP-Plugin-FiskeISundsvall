<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Fishingsundsvall
 * @subpackage Sk_Fishingsundsvall/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sk_Fishingsundsvall
 * @subpackage Sk_Fishingsundsvall/includes
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Fishingsundsvall_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sk-fishingsundsvall',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
