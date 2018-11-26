<?php
/**
 * Plugin Name:     Rise Shine Import
 * Plugin URI:      Import taxonomy, post
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     rise-shine-import
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Rise_Shine_Import
 */

define('RSIPATH', dirname( __FILE__ ));
define('RSIURL', plugin_dir_url(__FILE__));

add_action('init', 'rise_shine_import_init');

function rise_shine_import_init() {
  // Include
  include_once dirname( __FILE__ ) . '/includes/admin/class-rise-shine-import-admin-importers.php';
}
