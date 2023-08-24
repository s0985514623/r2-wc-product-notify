<?php

//Woo 自訂日期屬性
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
