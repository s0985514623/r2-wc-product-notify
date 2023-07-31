<?php
// $test='test';
// var_dump($test);
// require_once __DIR__ . '/../../assets/js/r2-wc-ajax.js';



add_action('admin_enqueue_scripts', 'enqueue_r2_wc_ajax');
function enqueue_r2_wc_ajax()
{
	// 添加自定义 JavaScript 文件，并自动加载 WordPress 默认的 jQuery 版本号
	wp_enqueue_script('r2-wc-ajax', home_url() . '/wp-content/plugins/r2-wc-product-notify/assets/js/r2-wc-ajax.js', array('jquery'), false, true);
}


//1.添加屬性類型=>後台，使後台可以選擇屬性類型
add_filter('product_attributes_type_selector', 'rudr_add_attr_type');

function rudr_add_attr_type($types)
{

	// let's add a color here!
	$types['color_type'] = 'Color'; // "color_type" // is just a custom slug
	return $types;
}

//2.將屬性選擇器字段添加到屬性編輯頁面
//=>在編輯屬性頁面
add_action('pa_color_edit_form_fields', 'rudr_edit_fields', 10, 2);

function rudr_edit_fields($term, $taxonomy)
{

	// do nothing if this term isn't the Color type
	global $wpdb;

	$attribute_type = $wpdb->get_var(
		$wpdb->prepare(
			"
			SELECT attribute_type
			FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies
			WHERE attribute_name = '%s'
			",
			substr($taxonomy, 3) // remove "pa_" prefix
		)
	);

	// if it is not a color attribute, just do nothing
	if ('color_type' !== $attribute_type) {
		return;
	}

	// otherwise let's display our colorpicker field
	// we can use attribute type as a meta key why not
	$color = get_term_meta($term->term_id, 'color_type', true);

?>
	<tr class="form-field">
		<th><label for="term-color_type">Color</label></th>
		<td><input type="text" id="term-color_type" name="color_type" value="<?php echo esc_attr($color) ?>" /></td>
	</tr>
<?php

}
//=>在新增屬性頁面


//3.保存屬性類型的值
//=>在編輯屬性頁面
add_action('edited_pa_color', 'rudr_save_color');
function rudr_save_color($term_id)
{

	$color_type = !empty($_POST['color_type']) ? $_POST['color_type'] : '';
	update_term_meta($term_id, 'color_type', sanitize_hex_color($color_type));
}

//=>在新增屬性頁面

//4.商品編輯頁面顯示屬性類型的值
add_action('woocommerce_product_option_terms', 'rudr_attr_select', 10, 3);

function rudr_attr_select($attribute_taxonomy, $i, $attribute)
{

	// do nothing if it is not our custom attribute type
	if ('color_type' !== $attribute_taxonomy->attribute_type) {
		return;
	}

	// get current values
	$options = $attribute->get_options();
	$options = !empty($options) ? $options : array();

?>
	<select multiple="multiple" data-placeholder="Select color" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i ?>][]">
		<?php
		$colors = get_terms('pa_color', array('hide_empty' => 0));
		if ($colors) {
			foreach ($colors as $color) {
				echo '<option value="' . $color->term_id . '"' . wc_selected($color->term_id, $options) . '>' . $color->name . '</option>';
			}
		}
		?>
	</select>
	<button class="button plus select_all_attributes">Select all</button>
	<button class="button minus select_no_attributes">Select none</button>
	<button class="button fr plus r2_add_new_attribute"><?php esc_html_e('Add new', 'woocommerce'); ?></button>
<?php
}

// 添加自定義商品欄位=>簡易商品
// function custom_add_product_fields()
// {

// 	// 添加一個自定義欄位 "重量"
// 	woocommerce_wp_text_input(array(
// 		'id'          => '_custom_weight',
// 		'label'       => '重量',
// 		'placeholder' => '輸入重量',
// 		'type'        => 'date', // 這裡設定為數字類型
// 		'desc_tip'    => true,
// 	));
// }
// add_action('woocommerce_product_options_general_product_data', 'custom_add_product_fields');

// // 儲存自定義商品欄位的值
// function custom_save_product_fields($product)
// {

// 	// 儲存 "重量" 欄位的值
// 	if (isset($_POST['_custom_weight'])) {
// 		$product->update_meta_data('_custom_weight', floatval($_POST['_custom_weight']));
// 	}
// }
// add_action('woocommerce_admin_process_product_object', 'custom_save_product_fields');

// // 顯示自定義商品欄位的值在前端
// function custom_display_product_fields()
// {
// 	global $product;

// 	// 取得 "重量" 欄位的值

// 	$custom_weight = $product->get_meta('_custom_weight');
// 	// 顯示在商品頁面
// 	echo '<div>';
// 	if (!empty($custom_weight)) {
// 		echo '<strong>重量:</strong> ' . esc_html($custom_weight) . ' kg<br>';
// 	}
// 	echo '</div>';
// }
// add_action('woocommerce_single_product_summary', 'custom_display_product_fields', 25);