<?php
/**
 * AJAX class
 */

declare(strict_types=1);

namespace R2\WC_Product_Notify;

use R2\WC_Product_Notify\CronSetting\R2_cron;

/**
 * AJAX class
 */
final class Ajax {
	const GET_POST_META_ACTION = 'send_preview_mail';

	/**
	 * Construct function
	 */
	public function __construct() {
		foreach ( array( self::GET_POST_META_ACTION ) as $action ) {
			\add_action( 'wp_ajax_' . $action, array( $this, $action . '_callback' ) );
			\add_action( 'wp_ajax_nopriv_' . $action, array( $this, $action . '_callback' ) );
		}
	}
	/**
	 * Send_preview_mail function
	 *
	 * @return void
	 */
	public function send_preview_mail_callback() {
		// Security check.
		check_ajax_referer( 'r2_wc_ajax_nonce', 'nonce' );
		if ( isset( $_POST['product_id'] ) ) {
			$operator_mail = empty( get_option( 'r2_notify_operator_mail' ) ) ? get_option( 'admin_email' ) : get_option( 'r2_notify_operator_mail' );
			$product_id    = $_POST['product_id'];
			// 取得商品對象.
			$product = wc_get_product( $product_id );
			// 取得該訂單商品的原始商品名稱.
			$product_name = $product->get_name();
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
			// 如果商品沒有日期屬性.
			if ( ! $has_target_attribute ) {
				status_header( 400 );
				$return = array(
					'message' => 'Not pa_date error',
					'data'    => array(
						'has_target_attribute' => $has_target_attribute,
						'target_value'         => $target_value,
						'$_POST'               => $_POST,
					),
				);
				wp_send_json( $return );
				wp_die();
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
			if ( isset( $_POST['variation_id'] ) ) {
				$variation_id = $_POST['variation_id'];
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
			\R2\WC_Product_Notify\OrderCompleteSendEmail\send_content( $send_mail_obj );
			// 處理Mail template.
			ob_start();
			include R2_WC_PRODUCT_NOTIFY_DIR . 'assets/templates/email/notifyEmail.php';
			// 當前時間.
			$order_date = \date_i18n( 'Y-m-d', \current_time( 'timestamp' ) );
			$content    = ob_get_clean();
			$mail       = new R2_cron();
			$mail->set_cron_schedule( $operator_mail, $order_date, $content, $course_info );

			$return = array(
				'message' => 'second success',
				'data'    => array(
					'product_id'   => $product_id,
					'variation_id' => $variation_id,
					'$_POST'       => $_POST,
				),
			);
			\wp_send_json( $return );
			\wp_die();
		} else {
			status_header( 400 );
			$return = array(
				'message' => 'Not Set product_id error',
				'data'    => array(
					'product_id' => $product_id,
					'$_POST'     => $_POST,
				),
			);
			wp_send_json( $return );
			wp_die();
		}
	}
}
new Ajax();
