<?php

//Send email when order is completed
// namespace R2\WC_Product_Notify\order;

add_action('woocommerce_order_status_completed', 'send_custom_email_on_order_completed', 10, 1);
function send_custom_email_on_order_completed($order_id)
{
	$order = new WC_Order($order_id);
	$email = $order->data['billing']['email'];
	$order_items = $order->get_items();
	foreach ($order_items as $item_id => $item) {
		// 取得商品ID
		$product_id = $item->get_data()['product_id'];
		// 檢查商品是否為可變商品
		if ($product_id && $product = wc_get_product($product_id)) {
			if ($product->is_type('variable')) {
				// 取得可變商品的變體（選項）
				$variation_id = $item->get_variation_id();
				$variation = wc_get_product($variation_id);
				$variations = $variation->get_variation_attributes();
				// 在這裡可以處理變體的屬性
				foreach ($variations as $attribute => $value) {
					// $attribute 是屬性名稱，$value 是屬性值，取得pa_date的值
					if ($attribute === 'attribute_pa_date') {
						$date = $value;

						$content_type = function () {
							return 'text/html';
						};
						add_filter('wp_mail_content_type', $content_type);
						ob_start();
						include R2_WC_Product_Notify_DIR . 'assets/templates/email/order-date-notify.php';
						$content = ob_get_clean();
						wp_mail($email, 'test', $content);
						remove_filter('wp_mail_content_type', $content_type);
					}
				}
			}
		}
	}
}
