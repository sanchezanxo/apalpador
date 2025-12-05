<?php
/**
 * Uninstall script for Apalpador plugin.
 *
 * This file is executed when the plugin is deleted from WordPress.
 * It removes all plugin data from the database.
 *
 * @package Apalpador
 */

// Exit if uninstall not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'apalpador_options' );

// For multisite: delete options from all sites.
if ( is_multisite() ) {
	$sites = get_sites();
	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		delete_option( 'apalpador_options' );
		restore_current_blog();
	}
}
