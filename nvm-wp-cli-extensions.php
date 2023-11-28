<?php
/**
 * Plugin Name: NVM WP-CLI Extensions
 * Description: Custom WP-CLI commands for extended functionality.
 * Version: 1.0
 * Author: Nevma
 */

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/commands/class-export-products-command.php';
	require_once __DIR__ . '/commands/class-update-products-from-csv-command.php';
	require_once __DIR__ . '/commands/class-set-outofstock-command.php';
	require_once __DIR__ . '/commands/class-delete-all-products-command.php';
}
