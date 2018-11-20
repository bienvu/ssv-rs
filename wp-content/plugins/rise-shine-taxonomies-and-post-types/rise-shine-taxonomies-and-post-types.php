<?php
/**
 * Plugin Name:     Rise Shine Taxonomies And Post Types
 * Description:     Create custom taxonomies and post types.
 * Author:          SentiusSSV
 * Text Domain:     rise-shine-taxonomies-and-post-types
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Rise_Shine_Taxonomies_And_Post_Types
 */

define('RISEANDSHINE_TPT_PLUGIN_PATH', plugin_dir_path(__FILE__));

include(RISEANDSHINE_TPT_PLUGIN_PATH. 'taxonomies/fabric.php');
include(RISEANDSHINE_TPT_PLUGIN_PATH. 'taxonomies/product-brand.php');
