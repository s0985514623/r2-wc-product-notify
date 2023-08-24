<?php

//Woo 自訂日期屬性
// namespace R2\WC_Product_Notify\AttributesSetting;

add_action('admin_enqueue_scripts', 'enqueue_r2_wc_ajax');
function enqueue_r2_wc_ajax()
{
	// 添加自定义 JavaScript 文件，并自动加载 WordPress 默认的 jQuery 版本号
	wp_enqueue_script('r2-wc-ajax', home_url() . '/wp-content/plugins/r2-wc-product-notify/assets/js/r2-wc-ajax.js', array('jquery'), false, true);
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style('wp-jquery-ui-dialog');
}


//1.添加屬性類型=>後台，使後台可以選擇屬性類型
add_filter('product_attributes_type_selector', 'rudr_add_attr_type');

function rudr_add_attr_type($types)
{
	// let's add a date here!
	$types['Date_type'] = '日期'; // "date_type" // is just a custom slug
	return $types;
}

//2.將屬性選擇器jQuery添加到屬性編輯頁面
//=>在編輯屬性頁面
add_action('pa_date_edit_form_fields', 'rudr_edit_fields', 10, 2);
function rudr_edit_fields($term, $taxonomy)
{

	// do nothing if this term isn't the date type
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

	// if it is not a date attribute, just do nothing
	if ('Date_type' !== $attribute_type) {
		return;
	}
?>
	<script>
		(function($) {
			$(function() {
				const tagName = $('#tag-name')
				tagName.datepicker({
					dateFormat: "yy/mm/dd"
				});
			})
		})(jQuery)
	</script>
<?php

}

//=>在新增屬性頁面
add_action('pa_date_add_form_fields', 'rudr_add_fields');
function rudr_add_fields($taxonomy)
{
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
	// if it is not a date attribute, just do nothing
	if ('Date_type' !== $attribute_type) {
		return;
	}
?>
	<script>
		(function($) {
			$(function() {
				const tagName = $('#tag-name')
				tagName.datepicker({
					dateFormat: "yy/mm/dd"
				});
			})
		})(jQuery)
	</script>
<?php
}


//3.商品編輯頁面顯示屬性類型的值
add_action('woocommerce_product_option_terms', 'rudr_attr_select', 10, 3);
function rudr_attr_select($attribute_taxonomy, $i, $attribute)
{

	// do nothing if it is not our custom attribute type
	if ('Date_type' !== $attribute_taxonomy->attribute_type) {
		return;
	}

	// get current values
	$options = $attribute->get_options();
	$options = !empty($options) ? $options : array();
	$attribute_orderby = !empty($attribute_taxonomy->attribute_orderby) ? $attribute_taxonomy->attribute_orderby : 'name';
?>
	<select multiple="multiple" data-minimum_input_length="0" data-limit="50" data-return_id="id" data-placeholder="請選擇日期" data-orderby="<?php echo esc_attr($attribute_orderby); ?>" class="multiselect attribute_values wc-taxonomy-term-search r2-product-date" name="attribute_values[<?php echo esc_attr($i); ?>][]" data-taxonomy="<?php echo esc_attr($attribute->get_taxonomy()); ?>">
		<?php
		$selected_terms = $attribute->get_terms('pa_date', array('hide_empty' => 0));
		if ($selected_terms) {
			foreach ($selected_terms as $selected_term) {
				/**
				 * Filter the selected attribute term name.
				 *
				 * @since 3.4.0
				 * @param string  $name Name of selected term.
				 * @param array   $term The selected term object.
				 */
				echo '<option value="' . esc_attr($selected_term->term_id) . '" selected="selected">' . esc_html(apply_filters('woocommerce_product_attribute_term_name', $selected_term->name, $selected_term)) . '</option>';
			}
		}
		?>
	</select>
	<button class="button plus select_all_attributes"><?php esc_html_e('Select all', 'woocommerce'); ?></button>
	<button class="button minus select_no_attributes"><?php esc_html_e('Select none', 'woocommerce'); ?></button>
	<button class="button fr plus r2_add_new_attribute"><?php esc_html_e('Add new', 'woocommerce'); ?></button>
	<!-- 隱藏的 div 作為 Dialog 的內容容器 -->
	<div id="datepickerDialog" style="display: none;">
		<input type="text" id="datepickerInput" value="">
	</div>

<?php
}

/*
會需要有以下變數
1.上課日期 v
2.商品名稱 v
3.時間與入場時間 x (全局or局部?)
4.地點與教室 x (全局or局部?)
5.表單填寫截止時間 x (全局or局部?)
6. 是否禁止用餐（依照地點規定）x (全局or局部?)
*/
//可變商品變化類型加入自定義欄位
add_action('woocommerce_product_after_variable_attributes', 'rudr_field', 10, 3);

function rudr_field($loop, $variation_data, $variation)
{

	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field[' . $loop . ']',
			'label'         => '自定義欄位',
			'wrapper_class' => 'form-row',
			'placeholder'   => '在此輸入內容...',
			'desc_tip'      => true,
			'description'   => '可以加入一些自定義的欄位',
			'value'         => get_post_meta($variation->ID, 'r2_notify_text', true)
		)
	);
}

//儲存自定義值
add_action('woocommerce_save_product_variation', 'rudr_save_fields', 10, 2);
function rudr_save_fields($variation_id, $loop)
{
	// Text Field
	$text_field = !empty($_POST['text_field'][$loop]) ? $_POST['text_field'][$loop] : '';
	update_post_meta($variation_id, 'r2_notify_text', sanitize_text_field($text_field));
}
