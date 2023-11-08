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
 * Version: 1.0.21
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

new R2\WC_Product_Notify\CronSetting\R2_cron;

register_activation_hook(__FILE__, 'r2_activate');
register_deactivation_hook(__FILE__, 'r2_deactivate');

function r2_activate()
{
	add_option('r2_notify_clock_before', '10', '', 'yes');
	add_option('r2_notify_days_before', '1', '', 'yes');
	// 檢查 WooCommerce 是否已安裝並且激活
	if (class_exists('WooCommerce')) {
		// 屬性名稱和slug
		$attribute_name = '課程日期';
		$attribute_slug = 'date';

		// 檢查屬性是否已經存在
		if (!taxonomy_exists('pa_' . sanitize_title($attribute_slug))) {
			// 屬性設置
			$attribute_args = array(
				'name' => $attribute_name,
				'slug' => $attribute_slug,
				'type' => 'Date_type', // 屬性類型
				'order_by' => 'menu_order', // 属性值的排序方式
				'has_archives' => false, // 是否启用属性的存档页面
			);
			// 创建属性
			wc_create_attribute($attribute_args);
		}
	}
}



function r2_deactivate()
{
	delete_option('r2_notify_clock_before');
	delete_option('r2_notify_days_before');
	delete_post_meta_by_key('r2_notify_text');
	$timestamp = wp_next_scheduled('r2_order_date_cron_Hook');
	wp_unschedule_event($timestamp, 'r2_order_date_cron_Hook');
}