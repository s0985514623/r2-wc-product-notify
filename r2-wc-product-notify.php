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
 * Version: 1.0.12
 * Author:            R2
 * License:           GPL-2.0+
 * Text Domain:       r2-wc-product-notify
 * Domain Path:       /languages
 */

// namespace R2\WC_Product_Notify;
if (!defined('R2_WC_Product_Notify_DIR')) {
	define('R2_WC_Product_Notify_DIR', plugin_dir_path(__FILE__));
}

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
require_once R2_WC_Product_Notify_DIR . '/inc/admin/wc-attributes.php';
require_once R2_WC_Product_Notify_DIR . '/inc/admin/wc-order.php';
require_once R2_WC_Product_Notify_DIR . '/inc/admin/order-date-cron.php';
require_once R2_WC_Product_Notify_DIR . '/inc/admin/settingPage.php';
require_once R2_WC_Product_Notify_DIR . '/inc/frontend/index.php';

R2\WC_Product_Notify\CronSetting\R2_cron::init();

register_activation_hook(__FILE__, 'bl_activate');
register_deactivation_hook(__FILE__, 'bl_deactivate');

function bl_activate()
{
	add_option('r2_notify_clock_before', '10', '', 'yes');
	add_option('r2_notify_days_before', '1', '', 'yes');
}



function bl_deactivate()
{
	delete_option('r2_notify_clock_before');
	delete_option('r2_notify_days_before');
	delete_post_meta_by_key('r2_notify_text');
	$timestamp = wp_next_scheduled('bl_order_date_cron_Hook');
	wp_unschedule_event($timestamp, 'bl_order_date_cron_Hook');
}
