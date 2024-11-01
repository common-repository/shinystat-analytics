<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.shinystat.com
 * @since      1.0.0
 *
 * @package    Shinystat_Analytics
 */


// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'shinystat_analytics_%';" );

