<?php
/**
 * Plugin Name:       R2 WC Product Notify
 * Plugin URI:        https://github.com/s0985514623/r2-wc-product-notify
 * Description:       為WC商品加入日期下單並在日期到來前做出Email提醒，或是在商品加入購物車時提醒.
 * Version:           1.1.30
 * Author:            R2
 * License:           GPL-2.0+
 * Text Domain:       r2-wc-product-notify
 */

/**
 * Tags: woocommerce, shop, order
 * Requires at least: 4.6
 * Tested up to: 4.8
 * Stable tag: 4.3
 */

namespace R2\WpMyAppPlugin\MyApp\Inc;

if ( ! defined( 'R2_WC_PRODUCT_NOTIFY_DIR' ) ) {
	define( 'R2_WC_PRODUCT_NOTIFY_DIR', plugin_dir_path( __FILE__ ) );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use R2\WC_Product_Notify\CronSetting\R2_cron;
if ( ! \class_exists( 'R2\WpMyAppPlugin\MyApp\Inc\Plugin' ) ) {
	/**
	 * Class Plugin
	 */
	final class Plugin {
		const KEBAB       = 'r2-wc-product-notify';
		const GITHUB_REPO = 'https://github.com/s0985514623/r2-wc-product-notify';
		/**
		 * Plugin Directory
		 *
		 * @var string
		 */
		public static $dir;

		/**
		 * Plugin URL
		 *
		 * @var string
		 */
		public static $url;
		/**
		 * Instance
		 *
		 * @var Plugin
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		public function __construct() {
			require_once R2_WC_PRODUCT_NOTIFY_DIR . '/inc/admin/wc-attributes.php';
			require_once R2_WC_PRODUCT_NOTIFY_DIR . '/inc/admin/wc-order.php';
			require_once R2_WC_PRODUCT_NOTIFY_DIR . '/inc/admin/order-date-cron.php';
			require_once R2_WC_PRODUCT_NOTIFY_DIR . '/inc/admin/settingPage.php';
			require_once R2_WC_PRODUCT_NOTIFY_DIR . '/inc/frontend/index.php';
			require_once R2_WC_PRODUCT_NOTIFY_DIR . '/inc/admin/class-ajax.php';
			\register_activation_hook( __FILE__, array( $this, 'r2_activate' ) );
			\register_deactivation_hook( __FILE__, array( $this, 'r2_deactivate' ) );
			\add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

			$this->plugin_update_checker();
		}
		/**
		 * Plugin update checker
		 *
		 * @return void
		 */
		public function plugin_update_checker(): void {
			$update_checker = PucFactory::buildUpdateChecker(
				self::GITHUB_REPO,
				__FILE__,
				self::KEBAB . '-release'
			);
			/**
			 * Type
			 *
			 * @var \Puc_v4p4_Vcs_PluginUpdateChecker $update_checker
			 */
			$update_checker->setBranch( 'master' );
			// if your repo is private, you need to set authentication
			// $update_checker->setAuthentication(self::$github_pat);
			$update_checker->getVcsApi()->enableReleaseAssets();
		}
		/**
		 * Check required plugins
		 *
		 * @return void
		 */
		public function plugins_loaded() {
			self::$dir = \untrailingslashit( \wp_normalize_path( \plugin_dir_path( __FILE__ ) ) );
			self::$url = \untrailingslashit( \plugin_dir_url( __FILE__ ) );
			new R2_cron();
		}


		/**
		 * Instance
		 *
		 * @return Plugin
		 */
		public static function instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Activate function
		 *
		 * @return void
		 */
		public function r2_activate() {
			add_option( 'r2_notify_clock_before', '10', '', 'yes' );
			add_option( 'r2_notify_days_before', '1', '', 'yes' );
			// 檢查 WooCommerce 是否已安裝並且激活
			if ( class_exists( 'WooCommerce' ) ) {
				// 屬性名稱和slug
				$attribute_name = '課程日期';
				$attribute_slug = 'date';

				// 檢查屬性是否已經存在
				if ( ! taxonomy_exists( 'pa_' . sanitize_title( $attribute_slug ) ) ) {
					// 屬性設置
					$attribute_args = array(
						'name'         => $attribute_name,
						'slug'         => $attribute_slug,
						'type'         => 'Date_type', // 屬性類型
						'order_by'     => 'menu_order', // 属性值的排序方式
						'has_archives' => false, // 是否启用属性的存档页面
					);
					// 创建属性
					\wc_create_attribute( $attribute_args );
				}
			}
		}


		/**
		 * Deactivate function
		 *
		 * @return void
		 */
		public function r2_deactivate() {
			delete_option( 'r2_notify_clock_before' );
			delete_option( 'r2_notify_days_before' );
			delete_post_meta_by_key( 'r2_notify_text' );
			$timestamp = wp_next_scheduled( 'r2_order_date_cron_Hook' );
			wp_unschedule_event( $timestamp, 'r2_order_date_cron_Hook' );
		}
	}
	Plugin::instance();

}
