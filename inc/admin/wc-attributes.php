<?php

//Woo 自訂日期屬性
namespace R2\WC_Product_Notify\AttributesSetting;

\add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_r2_wc_ajax');
function enqueue_r2_wc_ajax()
{
	// 添加自定义 JavaScript 文件，并自动加载 WordPress 默认的 jQuery 版本号
	wp_enqueue_script('r2-wc-ajax', home_url() . '/wp-content/plugins/r2-wc-product-notify/assets/js/r2-wc-ajax.js', array('jquery'), false, true);
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style('wp-jquery-ui-dialog');
}


//1.添加屬性類型=>後台，使後台可以選擇屬性類型
\add_filter('product_attributes_type_selector', __NAMESPACE__ . '\r2_add_attr_type');

function r2_add_attr_type($types)
{
	// let's add a date here!
	$types['Date_type'] = '日期'; // "date_type" // is just a custom slug
	return $types;
}

//2.將屬性選擇器jQuery添加到屬性編輯頁面
//=>在編輯屬性頁面
\add_action('pa_date_edit_form_fields', __NAMESPACE__ . '\r2_edit_fields', 10, 2);
function r2_edit_fields($term, $taxonomy)
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
\add_action('pa_date_add_form_fields', __NAMESPACE__ . '\r2_add_fields');
function r2_add_fields($taxonomy)
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
\add_action('woocommerce_product_option_terms', __NAMESPACE__ . '\r2_attr_select', 10, 3);
function r2_attr_select($attribute_taxonomy, $i, $attribute)
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
		<input type="text" id="datepickerInput" value="&nbsp">
		<!--input value值使儲存的欄位不為空，以解決無法儲存問題 -->
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
// \add_action('woocommerce_product_options_general_product_data', __NAMESPACE__ . '\misha_option_group');

// function misha_option_group()
// {
// 	echo '<div class="option_group">test</div>';
// }
//加入提醒資訊頁籤
\add_filter('woocommerce_product_data_tabs', __NAMESPACE__ . '\r2_product_settings_tabs');
function r2_product_settings_tabs($tabs)
{

	//unset( $tabs[ 'inventory' ] );

	$tabs['r2_notify'] = array(
		'label'    => '提醒資訊',
		'target'   => 'r2_notify_data',
		'class'    => array('show_if_simple', 'show_if_variable'),
		'priority' => 60,
	);
	return $tabs;
}

//提醒資訊內容(商品通用內容及信件範本)
\add_action('woocommerce_product_data_panels', __NAMESPACE__ . '\r2_product_panels');
function r2_product_panels()
{
?>
	<div id="r2_notify_data" class="panel woocommerce_options_panel hidden">
		<!-- 是否啟用 -->
		<?php
		woocommerce_wp_checkbox(
			array(
				'id'            => 'r2_is_enable',
				'value'         => get_post_meta(get_the_ID(), 'r2_is_enable', true),
				'label'         => '是否啟用信件通知功能',
				'description'   => '勾選後，請按下更新或發布鍵',
				'required'  => true

			)
		);
		if (get_post_meta(get_the_ID(), 'r2_is_enable', true) !== 'yes') {
			echo '</div>';
			return; // 未勾選就不顯示下面的欄位
		}
		?>
		<!-- 信件模板 -->
		<?php
		woocommerce_wp_select(
			array(
				'id'            => 'r2_email_template',
				'value'         => get_post_meta(get_the_ID(), 'r2_email_template', true),
				'label'         => '信件模板',
				'options'       => array(
					'' => '請選擇信件模板',
					'ad_template' => '廣告投放課(線上+線下+直播)',
					'phone_template' => '手機短影音(線上+線下)',
					'offline_template' => '其他實體課(線下)',
				),
			)
		); ?>
		<!--線上課程標題 -->
		<?php woocommerce_wp_text_input(
			array(
				'id'          => 'r2_online_title',
				'value'       => get_post_meta(get_the_ID(), 'r2_online_title', true),
				'label'       => '線上課程標題',
				'description' => '輸入【上課資訊－線上課程】or【上課資訊－預錄補充教材】等完整標題。如果無線上課程，請留空'
			)
		);
		?>
		<!-- 線上課程內容 -->
		<div class="r2_notify_wrap"><span>線上課程內容</span>
			<?= wp_editor(get_post_meta(get_the_ID(), 'r2_online_content', true), 'r2_online_content'); ?>
		</div>
		<hr>

		<!-- 直播內容標題 -->
		<?php
		woocommerce_wp_text_input(
			array(
				'id'          => 'r2_live_time_title',
				'value'       => get_post_meta(get_the_ID(), 'r2_live_time_title', true),
				'label'       => '直播內容標題',
				'description' => '輸入【課前與課後直播時間】等完整標題。如果無直播課程，請留空'
			)
		);
		?>
		<!-- 直播內容與時間 -->
		<div class="r2_notify_wrap"><span>直播內容與時間內容</span>
			<?php wp_editor(get_post_meta(get_the_ID(), 'r2_live_time', true), 'r2_live_time');
			?>
		</div>

	</div>
	<style>
		#r2_notify_data>div.r2_notify_wrap {
			padding: 0 13px;
			margin-bottom: 20px
		}

		#r2_notify_data>div.r2_notify_wrap>span {
			font-size: 12px;
			margin-bottom: 10px;
			display: inline-block;
		}
	</style>
<?php
}


\add_action('woocommerce_process_product_meta', __NAMESPACE__ . '\r2_save_fields_product_meta');
function r2_save_fields_product_meta($id)
{
	update_post_meta($id, 'r2_is_enable', sanitize_text_field($_POST['r2_is_enable']));

	isset($_POST['r2_email_template']) && update_post_meta($id, 'r2_email_template', $_POST['r2_email_template']);
	isset($_POST['r2_online_title']) && update_post_meta($id, 'r2_online_title', sanitize_text_field($_POST['r2_online_title']));
	isset($_POST['r2_online_content']) && update_post_meta($id, 'r2_online_content', $_POST['r2_online_content']);
	isset($_POST['r2_live_time_title']) && update_post_meta($id, 'r2_live_time_title', sanitize_text_field($_POST['r2_live_time_title']));
	isset($_POST['r2_live_time']) && update_post_meta($id, 'r2_live_time', $_POST['r2_live_time']);
}


//可變商品變化類型加入自定義欄位
\add_action('woocommerce_product_after_variable_attributes', __NAMESPACE__ . '\r2_field', 10, 3);

function r2_field($loop, $variation_data, $variation)
{
	//表單連結
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field_link[' . $loop . ']',
			'label'         => '表單連結',
			'wrapper_class' => 'form-row',
			'placeholder'   => '在此輸入表單連結',
			'desc_tip'      => true,
			'description'   => '可以直接輸入網址，將會自動轉換成超連結',
			'value'         => get_post_meta($variation->ID, 'r2_form_link', true)
		)
	);
	//表單備註
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field_note[' . $loop . ']',
			'label'         => '表單備註',
			'wrapper_class' => 'form-row',
			'placeholder'   => '在此輸入表單備註',
			'desc_tip'      => true,
			'description'   => '在此輸入表單備註',
			'value'         => get_post_meta($variation->ID, 'r2_form_note', true)
		)
	);
	//課程時間
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field_time[' . $loop . ']',
			'label'         => '課程時間',
			'wrapper_class' => 'form-row',
			'placeholder'   => '在此輸入課程時間',
			'desc_tip'      => true,
			'description'   => '在此輸入課程時間',
			'value'         => get_post_meta($variation->ID, 'r2_course_time', true)
		)
	);
	//課程地點
	woocommerce_wp_text_input(
		array(
			'id'            => 'text_field_location[' . $loop . ']', //id要不一樣
			'label'         => '課程地點',
			'wrapper_class' => 'form-row',
			'placeholder'   => '在此輸入課程地點',
			'desc_tip'      => true,
			'description'   => '在此輸入課程地點',
			'value'         => get_post_meta($variation->ID, 'r2_course_location', true)
		)
	);
	//課程備註
	woocommerce_wp_textarea_input(
		array(
			'id'          => 'r2_course_note[' . $loop . ']',
			'value'       => get_post_meta($variation->ID, 'r2_course_note', true),
			'label'       => '課程備註',
			'desc_tip'    => true,
			'rows'        => 5,
			'description' => '課程備註',
		)
	);
	//無法在ajax後初始化TinyMCE編輯器
	// $settings = array('textarea_name' => 'r2_course_note');
	// wp_editor(get_post_meta($variation->ID, 'r2_course_note2', true), 'r2_course_note2' . $loop . '');
?>

<?php
}
//儲存自定義值
\add_action('woocommerce_save_product_variation', __NAMESPACE__ . '\r2_save_fields_variation', 10, 2);
function r2_save_fields_variation($variation_id, $loop)
{
	//Link Field
	$text_field_link = !empty($_POST['text_field_link'][$loop]) ? $_POST['text_field_link'][$loop] : '';
	update_post_meta($variation_id, 'r2_form_link', sanitize_text_field($text_field_link));

	//Note Field
	$text_field_note = !empty($_POST['text_field_note'][$loop]) ? $_POST['text_field_note'][$loop] : '';
	update_post_meta($variation_id, 'r2_form_note', sanitize_text_field($text_field_note));

	// Time Field
	$text_field_time = !empty($_POST['text_field_time'][$loop]) ? $_POST['text_field_time'][$loop] : '';
	update_post_meta($variation_id, 'r2_course_time', sanitize_text_field($text_field_time));

	// location Field
	$text_field_location = !empty($_POST['text_field_location'][$loop]) ? $_POST['text_field_location'][$loop] : '';
	update_post_meta($variation_id, 'r2_course_location', sanitize_text_field($text_field_location));

	//Course Note Field
	$text_field_course_note = !empty($_POST['r2_course_note'][$loop]) ? $_POST['r2_course_note'][$loop] : '';
	update_post_meta($variation_id, 'r2_course_note', $text_field_course_note);
	// //Course Note2 Field
	// $text_field_course_note2 = !empty($_POST['r2_course_note2' . $loop]) ? $_POST['r2_course_note2' . $loop] : '';
	// update_post_meta($variation_id, 'r2_course_note2', $text_field_course_note2);
}
