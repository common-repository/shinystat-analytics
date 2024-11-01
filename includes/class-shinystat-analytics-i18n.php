<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.shinystat.com
 * @since      1.0.0
 *
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Shinystat_Analytics
 * @subpackage Shinystat_Analytics/includes
 * @author     ShinyStat <support@shinystat.com>
 */
class Shinystat_Analytics_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'shinystat-analytics',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
