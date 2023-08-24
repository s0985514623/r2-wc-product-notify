<?php

namespace R2\WC_Product_Notify\CronSetting;

class R2_cron
{

	protected static $instance;
	public static function init()
	{
		self::$instance = new self();
		add_action('bl_order_date_cron_Hook', array(self::$instance, 'bl_order_date_cron_Exec'), 10, 4);
	}
	// public static function init()
	// {
	// 	// add_filter('cron_schedules', array($this, 'r2_add_cron_interval'));
	// 	add_action('bl_order_date_cron_Hook', array($this, 'bl_order_date_cron_Exec'), 10, 4);
	// }


	public function set_cron_schedule($to, $orderDate, $content, $productName)
	{
		$clock = get_option('r2_notify_clock_before', 10);
		$day_before = get_option('r2_notify_days_before', 1);
		$orderDateTimestamp = strtotime($orderDate);
		//寄送日期 = orderDate - day_before
		$sendDate = wp_date("Y-m-d", strtotime($orderDate . " -" . $day_before . " day"));

		$date = $sendDate . " " . $clock . ":00:00";
		$dateTime = new \DateTime($date, wp_timezone());
		$timestamp = $dateTime->getTimestamp();
		//什麼時候寄，寄給誰，內容，開課日期，商品名稱
		if (!wp_next_scheduled('bl_order_date_cron_Hook', array($to, $content, $orderDateTimestamp, $productName))) {
			wp_schedule_single_event($timestamp, 'bl_order_date_cron_Hook', array($to, $content, $orderDateTimestamp, $productName));
		}
	}

	public function bl_order_date_cron_Exec(
		$to = "s0985514623@gmail.com",
		$content = "開課通知",
		$orderDateTimestamp,
		$productName
	) {
		$subject = '【重要！課前通知】' . wp_date('n/j(D)', $orderDateTimestamp) . " " . $productName;

		$content_type = function () {
			return 'text/html';
		};
		add_filter('wp_mail_content_type', $content_type);
		wp_mail($to, $subject, $content);
		remove_filter('wp_mail_content_type', $content_type);
	}
}
