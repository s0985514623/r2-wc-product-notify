<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           R2 WC Product Notify
 *
 * @wordpress-plugin
 * Plugin Name:       R2 WC Product Notify
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       為WC商品加入日期下單並在日期到來前做出Email提醒，或是在商品加入購物車時提醒.
 * Version: 1.0.2
 * Author:            R2
 * License:           GPL-2.0+
 * Text Domain:       r2-wc-product-notify
 * Domain Path:       /languages
 */

namespace R2\WC_Product_Notify;

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
require_once plugin_dir_path(__FILE__) . '/inc/admin/index.php';
require_once plugin_dir_path(__FILE__) . '/inc/frontend/index.php';
