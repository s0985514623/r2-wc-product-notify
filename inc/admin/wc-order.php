<?php

//Send email when order is completed
namespace R2\WC_Product_Notify\OrderCompleteSendEmail;

use R2\WC_Product_Notify\CronSetting\R2_cron;
//訂單完成後action
\add_action('woocommerce_order_status_completed', __NAMESPACE__ . '\send_custom_email_on_order_completed', 10, 1);
function send_custom_email_on_order_completed($order_id)
{

	$order = new \WC_Order($order_id);
	$email = $order->data['billing']['email'];
	//循環商品 $itemArray => Object
	foreach ($order->get_items() as $itemArray) {
		// 透過itemArray取得商品WC_Order_Item_Product實例
		$item = new \WC_Order_Item_Product($itemArray);
		//取得商品ID
		$product_id = $item->get_product_id();
		//取得該訂單商品的原始商品名稱
		$productName = wc_get_product($product_id)->get_name();
		//取得商品的是否啟用信用通知設定 =>yes || ''
		$is_enable = get_post_meta($product_id, 'r2_is_enable', true);
		//如果商品沒有啟用則跳過orderItem循環
		if ($is_enable !== 'yes') {
			continue;
		}
		//取得變體ID
		$variation_id = $item->get_variation_id();
		/**
		 * @var \WC_Product_Variation $variation =>改善vscode會提示 $variation is not defined錯誤
		 */
		// 檢查商品是否為可變商品，並且取得變體商品
		if ($variation_id && $variation = wc_get_product($variation_id)) {
			//由變體類型取得商品屬性
			$attributes = $variation->get_variation_attributes();
			// 循環取得商品屬性
			foreach ($attributes as $attribute => $value) {
				// $attribute 是屬性名稱，$value 是屬性值，取得pa_date(日期)的值
				if ($attribute === 'attribute_pa_date') {
					//取得商品選擇日期
					$orderDate = $value;
					//重組日期格式
					$orderDateTimestamp = strtotime($orderDate);
					//課程資訊info=>日期加上商品名稱
					$courseInfo = wp_date('n/j(D)', $orderDateTimestamp) . " " . $productName;
					//取得原始商品的資料
					//信件模板=>
					// 'ad_template' => '廣告投放課(線上+線下+直播)',
					// 'phone_template' => '手機短影音(線上+線下)',
					// 'offline_template' => '其他實體課(線下)',
					$email_template = get_post_meta($product_id, 'r2_email_template', true);
					//取得商品的線上課程標題
					$online_title = get_post_meta($product_id, 'r2_online_title', true);
					//取得商品的線上課程內容
					$online_content = get_post_meta($product_id, 'r2_online_content', true);
					//取得商品的直直播內容標題
					$live_time_title = get_post_meta($product_id, 'r2_live_time_title', true);
					//取得商品的直播內容與時間
					$live_time = get_post_meta($product_id, 'r2_live_time', true);

					//取得變體類型的資料
					//表單連結
					$form_link = get_post_meta($variation_id, 'r2_form_link', true);
					//取得表單備註
					$form_note = get_post_meta($variation_id, 'r2_form_note', true);
					//取得課程時間
					$course_time = get_post_meta($variation_id, 'r2_course_time', true);
					//取得課程地點
					$course_location = get_post_meta($variation_id, 'r2_course_location', true);
					//取得課程備註
					$course_note = get_post_meta($variation_id, 'r2_course_note', true);
					//將需要的變數整理成一個object
					$vars = new \stdClass();
					$vars->orderDate = $orderDate;
					$vars->courseInfo = $courseInfo;
					$vars->email_template = $email_template;
					$vars->online_title = $online_title;
					$vars->online_content = $online_content;
					$vars->live_time_title = $live_time_title;
					$vars->live_time = $live_time;
					$vars->form_link = $form_link;
					$vars->form_note = $form_note;
					$vars->course_time = $course_time;
					$vars->course_location = $course_location;
					$vars->course_note = $course_note;

					//把外部變數帶入function
					Send_Content($vars);
					//處理Mail template
					ob_start();
					include R2_WC_Product_Notify_DIR . 'assets/templates/email/notifyEmail.php';
					$content = ob_get_clean();
					$mail = new R2_cron;
					$mail->set_cron_schedule($email, $orderDate, $content, $courseInfo);
				}
			}
		}
	}
}
//自定義信件內容
function Send_Content($var)
{
	//插入自定義內容在woocommerce_email中
	add_action('custom_hook_name', function () use ($var) {
?>
		<div style="text-align: left;;padding:15px;font-size:13px;font-family:inherit;">
			<p style="text-decoration: underline;">【重要】為了更瞭解學員需求，煩請填寫此表單:</p>
			<a href=<?= $var->form_link ?> style="text-decoration: underline;"><?php echo $var->form_link; ?></a>
			<p style="color:red ;text-decoration: underline;"><?php echo $var->form_note; ?></p>
			<hr>
			<?php
			//如果為純線下課不會有線上課程
			if ($var->email_template !== 'offline_template') {
			?>
				<div class="online_course" style="text-align: left;">
					<p style="font-weight: bold;"><?php echo $var->online_title; ?></p>
					<p><?php echo $var->online_content; ?></p>
				</div>
				<br>
			<?php
			}
			?>
			<!-- 線下課資訊 -->
			<div class="offline_course" style="text-align: left;">
				<p style="font-weight: bold;">【上課資訊－實體課程】</p>
				<p>課程資訊：<?php echo $var->courseInfo ?></p>
				<p>課程時間：<?php echo $var->course_time ?></p>
				<p>課程地點：<a href="https://www.google.com/maps/place/<?= $var->course_location ?>" target="_blank"><?php echo $var->course_location; ?></a></p>
				<div style="text-align:left;"><?php echo $var->course_note; ?></div>
			</div>
			<br>
			<?php
			//如果為有直播課程
			if ($var->email_template === 'ad_template') {
			?>
				<div class="live_course" style="text-align: left;">
					<p style="font-weight: bold;"><?php echo $var->live_time_title; ?></p>
					<p><?php echo $var->live_time; ?></p>
				</div>
			<?php
			}
			?>
		</div>
		<style>
			p {
				margin: 6px 0px !important;
			}
		</style>
<?php
	}, 5);
}
