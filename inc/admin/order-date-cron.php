<?php

namespace R2\WC_Product_Notify\CronSetting;

class R2_cron
{
	public function __construct()
	{
		add_action('r2order_date_cron_Hook', array($this, 'r2order_date_cron_Exec'), 10, 4);
	}

	//進行cron排程
	public function set_cron_schedule($to, $orderDate, $content, $courseInfo)
	{
		//取得設定的時間(幾點)
		$clock = get_option('r2_notify_clock_before', 10);
		//取得設定的天數(幾天前)
		$day_before = get_option('r2_notify_days_before', 1);
		//寄送日期 = orderDate - day_before
		$sendDate = wp_date("Y-m-d", strtotime($orderDate . " -" . $day_before . " day"));

		//安排時間
		$date = $sendDate . " " . $clock . ":00:00";
		$dateTime = new \DateTime($date, wp_timezone());
		$timestamp = $dateTime->getTimestamp();
		$subject = '【重要！課前通知】' . $courseInfo;
		//什麼時候寄，寄給誰，內容，開課日期，商品名稱
		if (!wp_next_scheduled('r2order_date_cron_Hook', array($to, $subject, $content))) {
			wp_schedule_single_event($timestamp, 'r2order_date_cron_Hook', array($to, $subject, $content));
		}

		//同時在前三天也寄一次給操作人員
		//安排時間
		$operatorDate = wp_date("Y-m-d", strtotime($sendDate . " -3 day")) . " " . $clock . ":00:00";
		$operatorDateTime = new \DateTime($operatorDate, wp_timezone());
		$operatorTimestamp = $operatorDateTime->getTimestamp();
		//取得操作人員email,如果為空則預設為網站管理員
		$operator_mail = empty(get_option('r2_notify_operator_mail')) ? get_option('admin_email') : get_option('r2_notify_operator_mail');
		$operatorSubject = '【操作人員提前通知】-' . $subject;
		if (!wp_next_scheduled('r2order_date_cron_Hook', array($operator_mail, $operatorSubject, $content))) {
			wp_schedule_single_event($operatorTimestamp, 'r2order_date_cron_Hook', array($operator_mail, $operatorSubject, $content));
		}
	}

	public function r2order_date_cron_Exec(
		$to = "s0985514623@gmail.com",
		$subject = "",
		$content = "",
	) {
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($to, $subject, $content, $headers);
	}
}
