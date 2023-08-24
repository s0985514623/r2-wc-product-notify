<?php

//Send email when order is completed
// namespace R2\WC_Product_Notify\order;
// use WC_Order;
// use WC_Order_Item_Product;
// use WC_Product_Variation;
use R2\WC_Product_Notify\Cron\R2_cron;

add_action('woocommerce_order_status_completed', 'send_custom_email_on_order_completed', 10, 1);
function send_custom_email_on_order_completed($order_id)
{

	$order = new WC_Order($order_id);
	$email = $order->data['billing']['email'];
	//循環商品 $item => Object
	foreach ($order->get_items() as $item_id) {
		// 取得商品ID
		$item = new WC_Order_Item_Product($item_id);
		$variation_id = $item->get_variation_id();
		//取得該訂單商品的original_post_title
		$productName = wc_get_product($item->get_product_id())->get_name();
		/**
		 * @var WC_Product_Variation $variation =>改善vscode會提示 $variation is not defined錯誤
		 */
		// 檢查商品是否為可變商品，並且取得變體商品
		if ($variation_id && $variation = wc_get_product($variation_id)) {
			$variations = $variation->get_variation_attributes();
			// 循環取得商品屬性
			foreach ($variations as $attribute => $value) {
				// $attribute 是屬性名稱，$value 是屬性值，取得pa_date的值
				if ($attribute === 'attribute_pa_date') {
					//取得商品選擇日期
					$orderDate = $value;

					add_action('custom_hook_name', function ($obj, $email) {
						foreach ($obj->get_items() as $item_id) {
							// 取得商品ID
							$item = new WC_Order_Item_Product($item_id);
							$variation_id = $item->get_variation_id();
							$customText = get_post_meta($variation_id, 'r2_notify_text', true);
							echo '自定義欄位:' . $customText;
						}
					}, 5, 2);
					//處理Mail template
					ob_start();
					include R2_WC_Product_Notify_DIR . 'assets/templates/email/order-date-notify.php';
					$content = ob_get_clean();
					$mail = new R2_cron;
					$mail->set_cron_schedule($email, $orderDate, $content, $productName);
				}
			}
		}
	}
}
