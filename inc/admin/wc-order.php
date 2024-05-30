<?php
/**
 * Send email when order is completed
 */

namespace R2\WC_Product_Notify\OrderCompleteSendEmail;

use R2\WC_Product_Notify\CronSetting\R2_cron;
// 訂單完成後action.
\add_action( 'woocommerce_order_status_completed', __NAMESPACE__ . '\send_custom_email_on_order_completed', 10, 1 );

/**
 * Send email when order is completed
 *
 * @param int $order_id The ID of the order.
 * @return void
 */
function send_custom_email_on_order_completed( $order_id ) {

	$order = new \WC_Order( $order_id );
	$email = $order->data['billing']['email'];
	// 循環商品 $item_array => Object.
	foreach ( $order->get_items() as $item_array ) {
		// 透過item_array取得商品WC_Order_Item_Product實例.
		$item = new \WC_Order_Item_Product( $item_array );
		// 取得商品ID.
		$product_id = $item->get_product_id();
		// 取得商品對象.
		$product = wc_get_product( $product_id );
		// 取得該訂單商品的原始商品名稱.
		$product_name = $product->get_name();
		// 取得商品的是否啟用信用通知設定 =>yes || ''.
		$is_enable = get_post_meta( $product_id, 'r2_is_enable', true );
		// 如果商品沒有啟用信用通知則跳過orderItem foreach循環.
		if ( 'yes' !== $is_enable ) {
			continue;
		}
		// 取得商品屬性.
		$attributes = $product->get_attributes();
		// 檢查是否包含日期屬性.
		$target_attribute     = 'pa_date';
		$target_value         = '';
		$has_target_attribute = false;
		foreach ( $attributes as $attribute => $value ) {
			if ( $attribute === $target_attribute ) {
				$target_id            = $value->get_options()[0];
				$target_value         = get_term( $target_id )->name;
				$has_target_attribute = true;
				break;
			}
		}
		// 如果商品沒有日期屬性則跳過orderItem foreach循環.
		if ( ! $has_target_attribute ) {
			continue;
		}
		// 定義要傳入信件的變數.
		$send_mail_obj = new \stdClass();
		// 取得商品選擇日期.
		$order_date = $target_value;
		// 表單連結.
		$form_link = get_post_meta( $product_id, 'r2_text_field_link_simple', true );
		// 取得表單備註.
		$form_note = get_post_meta( $product_id, 'r2_text_field_note_simple', true );
		// 取得課程時間.
		$course_time = get_post_meta( $product_id, 'r2_text_field_time_simple', true );
		// 取得課程地點.
		$course_location = get_post_meta( $product_id, 'r2_text_field_location_simple', true );
		// 取得課程備註.
		$course_note = get_post_meta( $product_id, 'r2_course_note_simple', true );

		// 取得變體ID.
		$variation_id = $item->get_variation_id();

		// 如果有變體ID.
		if ( $variation_id ) {
			// 取得變體商品.
			// phpcs:ignore
			/**
			 * @var \WC_Product_Variation $variation =>改善vscode會提示 $variation is not defined錯誤
			 */
			$variation = wc_get_product( $variation_id );
			// 由變體類型取得商品屬性.
			$variation_attributes = $variation->get_variation_attributes();
			// 循環取得商品屬性.
			foreach ( $variation_attributes as $attribute => $value ) {
				// $attribute 是屬性名稱，$value 是屬性值，取得pa_date(日期)的值
				if ( 'attribute_' . $target_attribute === $attribute ) {
					// 重新設定日期.
					$order_date = $value;
				}
			}
			// 重新取得變體類型的資料
			// 表單連結.
			$form_link = get_post_meta( $variation_id, 'r2_form_link', true );
			// 取得表單備註.
			$form_note = get_post_meta( $variation_id, 'r2_form_note', true );
			// 取得課程時間.
			$course_time = get_post_meta( $variation_id, 'r2_course_time', true );
			// 取得課程地點.
			$course_location = get_post_meta( $variation_id, 'r2_course_location', true );
			// 取得課程備註.
			$course_note = get_post_meta( $variation_id, 'r2_course_note', true );
		}

		// 重組日期格式.
		$order_date_timestamp = strtotime( $order_date );
		// 課程資訊info=>日期加上商品名稱.
		$course_info = wp_date( 'n/j(D)', $order_date_timestamp ) . ' ' . $product_name;
		// 取得信件模板=>.
		// 'ad_template' => '廣告投放課(線上+線下+直播)',.
		// 'phone_template' => '手機短影音(線上+線下)',.
		// 'offline_template' => '其他實體課(線下)',.
		$email_template = get_post_meta( $product_id, 'r2_email_template', true );
		// 取得商品的線上課程標題.
		$online_title = get_post_meta( $product_id, 'r2_online_title', true );
		// 取得商品的線上課程內容.
		$online_content = get_post_meta( $product_id, 'r2_online_content', true );
		// 取得商品的直直播內容標題.
		$live_time_title = get_post_meta( $product_id, 'r2_live_time_title', true );
		// 取得商品的直播內容與時間.
		$live_time = get_post_meta( $product_id, 'r2_live_time', true );

		// 將需要的變數整理成一個object.
		$send_mail_obj->order_date      = $order_date;
		$send_mail_obj->course_info     = $course_info;
		$send_mail_obj->email_template  = $email_template;
		$send_mail_obj->online_title    = $online_title;
		$send_mail_obj->online_content  = $online_content;
		$send_mail_obj->live_time_title = $live_time_title;
		$send_mail_obj->live_time       = $live_time;
		$send_mail_obj->form_link       = $form_link;
		$send_mail_obj->form_note       = $form_note;
		$send_mail_obj->course_time     = $course_time;
		$send_mail_obj->course_location = $course_location;
		$send_mail_obj->course_note     = $course_note;

		// 把外部變數帶入function.
		send_content( $send_mail_obj );
		// 處理Mail template.
		ob_start();
		include R2_WC_PRODUCT_NOTIFY_DIR . 'assets/templates/email/notifyEmail.php';
		$content = ob_get_clean();
		$mail    = new R2_cron();
		$mail->set_cron_schedule( $email, $order_date, $content, $course_info );
	}
}

/**
 * Send_content function
 *
 * @param Object $send_mail_obj The object of send mail.
 * @return void
 */
function send_content( $send_mail_obj ) {
	// 插入自定義內容在woocommerce_email中.
	add_action(
		'custom_hook_name',
		function () use ( $send_mail_obj ) {
			?>
		<div style="text-align: left;;padding:15px;font-size:13px;font-family:inherit;">
			<p style="text-decoration: underline;">【重要】為了更瞭解學員需求，煩請填寫此表單:</p>
			<a href=<?php echo esc_url( $send_mail_obj->form_link ); ?> style="text-decoration: underline;"><?php echo esc_url( $send_mail_obj->form_link ); ?></a>
			<p style="color:red ;text-decoration: underline;"><?php echo esc_html( $send_mail_obj->form_note ); ?></p>
			<hr>
			<?php
			// 如果為純線下課不會有線上課程.
			if ( 'offline_template' !== $send_mail_obj->email_template ) {
				?>
				<div class="online_course" style="text-align: left;">
					<p style="font-weight: bold;"><?php echo esc_html( $send_mail_obj->online_title ); ?></p>
					<p><?php echo esc_html( $send_mail_obj->online_content ); ?></p>
				</div>
				<br>
				<?php
			}
			?>
			<!-- 線下課資訊 -->
			<div class="offline_course" style="text-align: left;">
				<p style="font-weight: bold;">【上課資訊－實體課程】</p>
				<p>課程資訊：<?php echo esc_html( $send_mail_obj->course_info ); ?></p>
				<p>課程時間：<?php echo esc_html( $send_mail_obj->course_time ); ?></p>
				<p>課程地點：<a href="https://www.google.com/maps/place/<?php echo esc_html( $send_mail_obj->course_location ); ?>" target="_blank"><?php echo esc_html( $send_mail_obj->course_location ); ?></a></p>
				<div style="text-align:left;"><?php echo esc_html( $send_mail_obj->course_note ); ?></div>
			</div>
			<br>
			<?php
			// 如果為有直播課程.
			if ( 'ad_template' === $send_mail_obj->email_template ) {
				?>
				<div class="live_course" style="text-align: left;">
					<p style="font-weight: bold;"><?php echo esc_html( $send_mail_obj->live_time_title ); ?></p>
					<p><?php echo esc_html( $send_mail_obj->live_time ); ?></p>
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
		},
		5
	);
}
