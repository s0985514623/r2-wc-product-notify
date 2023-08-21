<?php

namespace R2\WC_Product_Notify\Cron;

//1.帶入時間
class R2_cron
{
	public function init()
	{
		// add_filter('cron_schedules', array($this, 'r2_add_cron_interval'));
		add_action('bl_order_date_cron_Hook', array($this, 'bl_order_date_cron_Exec'), 10, 3);
	}


	// public function r2_add_cron_interval($schedules)
	// {
	// 	$schedules['five_seconds'] = array(
	// 		'interval' => 5,
	// 		'display'  => esc_html__('Every Five Seconds'),
	// 	);
	// 	return $schedules;
	// }

	public function set_cron_schedule($to, $subject, $message)
	{
		if (!wp_next_scheduled('bl_order_date_cron_Hook', array($to, $subject, $message))) {
			wp_schedule_single_event(time() + 60, 'bl_order_date_cron_Hook', array($to, $subject, $message));
		}

		// wp_schedule_single_event( time() + 3600, 'my_new_event', array( $arg1, $arg2, $arg3 ) );
	}

	public function bl_order_date_cron_Exec(
		$to = "s0985514623@gmail.com",
		$subject = "order_date_cron",
		$message = "test"
	) {
		$content_type = function () {
			return 'text/html';
		};
		add_filter('wp_mail_content_type', $content_type);
		wp_mail($to, $subject, $message);
		remove_filter('wp_mail_content_type', $content_type);
	}
}

$R2_cron = new R2_cron();
$R2_cron->init();


	// add_filter( 'cron_schedules', 'r2_add_cron_interval' );
	// function r2_add_cron_interval( $schedules ) {
	// 		$schedules['five_seconds'] = array(
	// 				'interval' => 5,
	// 				'display'  => esc_html__( 'Every Five Seconds' ), );
	// 		return $schedules;
	// }
	// function custom_daily_cron_schedule() {
	// if ( ! wp_next_scheduled( 'bl_order_date_cron_Hook' ) ) {
	// 	wp_schedule_event( time(), 'five_seconds', 'bl_order_date_cron_Hook' );
	// }
	// }

	// add_action( 'wp', 'custom_daily_cron_schedule' );

	// add_action( 'bl_order_date_cron_Hook', 'bl_order_date_cron_Exec' );
	// function bl_order_date_cron_Exec(){
	// 	wp_mail('s0985514623@gmail.com', 'order_date_cron', 'test');
	// }