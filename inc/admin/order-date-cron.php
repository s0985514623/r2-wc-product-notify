<?php

namespace R2\WC_Product_Notify\Cron;

//1.帶入時間
class R2_cron
{
	public function init()
	{
		// add_filter('cron_schedules', array($this, 'r2_add_cron_interval'));
		add_action('bl_order_date_cron_Hook', array($this, 'bl_order_date_cron_Exec'), 10, 2);
	}


	// public function r2_add_cron_interval($schedules)
	// {
	// 	$schedules['five_seconds'] = array(
	// 		'interval' => 5,
	// 		'display'  => esc_html__('Every Five Seconds'),
	// 	);
	// 	return $schedules;
	// }

	public function set_cron_schedule($to, $orderDate, $content)
	{
		$clock = get_option('r2_notify_clock_before', 10);
		$day_before = get_option('r2_notify_days_before', 1);
		//orderDate - day_before
		$orderDate = wp_date("Y-m-d", strtotime($orderDate . " -" . $day_before . " day"));

		$date = $orderDate . " " . $clock . ":00:00";
		$datetime = new \DateTime($date, wp_timezone());
		$timestamp = $datetime->getTimestamp();

		//寄給誰，什麼時候寄，內容
		if (!wp_next_scheduled('bl_order_date_cron_Hook', array($to, $content))) {
			wp_schedule_single_event($timestamp, 'bl_order_date_cron_Hook', array($to, $content));
			// wp_schedule_single_event(time(), 'bl_order_date_cron_Hook', array($to, $content));
		}
	}

	public function bl_order_date_cron_Exec(
		$to = "s0985514623@gmail.com",
		$content = "test"
	) {
		$content_type = function () {
			return 'text/html';
		};
		add_filter('wp_mail_content_type', $content_type);

		wp_mail($to, '【重要！課前通知】', $content);
		remove_filter('wp_mail_content_type', $content_type);
	}
}

$R2_cron = new R2_cron();
$R2_cron->init();
