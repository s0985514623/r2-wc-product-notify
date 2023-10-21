<?php
// 創建後台菜單頁面
namespace R2\WC_Product_Notify\BackstageSetting;

\add_action('admin_menu',  __NAMESPACE__ . '\r2_notify_menu_page');
function r2_notify_menu_page()
{
	add_menu_page(
		'課前提醒通知設定',       // 頁面標題
		'課前提醒通知設定',       // 菜單標題
		'manage_options',       // 權限等級
		'r2-wc-product-notify',       // 菜單的slug
		__NAMESPACE__ . '\r2_notify_page_content' // 回調函數，用於輸出頁面內容
	);
}


// 頁面內容
function r2_notify_page_content()
{
	// 如果用戶點擊了保存按鈕，則更新選項
	if (isset($_POST['r2_notify_save'])) {
		$days_before = sanitize_text_field($_POST['r2_notify_days_before']); // 獲取表單提交的值
		update_option('r2_notify_days_before', $days_before); // 更新選項
		$clock_before = sanitize_text_field($_POST['r2_notify_clock_before']); // 獲取表單提交的值
		update_option('r2_notify_clock_before', $clock_before); // 更新選項
		//如果有設定操作人員email則取得，否則預設為網站管理員
		$r2_notify_operator_mail = sanitize_text_field($_POST['r2_notify_operator_mail']);
		update_option('r2_notify_operator_mail', $r2_notify_operator_mail); // 更新選項
		echo '<div class="updated"><p>設置已保存。</p></div>';
	}

?>
	<link rel="stylesheet" href="<?= plugins_url('r2-wc-product-notify/assets/css/r2-notify-page-content.css') ?>">
	<div class="pageWrap">
		<h2>課前提醒通知設定</h2>
		<form method="post" action="">
			<div class="pageSection">
				<div><span style="display:block; width: 180px;">操作人員Email(將於發信前三天通知):</span></div>
				<div><input type="text" name="r2_notify_operator_mail" placeholder="預設為網站管理員" value="<?php echo esc_attr(get_option('r2_notify_operator_mail')); ?>"></div>
			</div>
			<div class="pageSection">
				<div><span style="display:block; width: 180px;">要在到期前幾天通知：</span></div>
				<div><input type="number" name="r2_notify_days_before" placeholder="預設為1天" value="<?php echo esc_attr(get_option('r2_notify_days_before', 1)); ?>"></div>
			</div>
			<div class="pageSection">
				<div><span style="display:block; width: 180px;">要在幾點通知：(24小時制)</span></div>
				<div><input type="number" name="r2_notify_clock_before" placeholder="預設為10點" value="<?php echo esc_attr(get_option('r2_notify_clock_before', 10)); ?>"></div>
			</div>
			<div class="pageSection">
				<button type="submit" name="r2_notify_save" class="button-primary">保存</button> <!-- 添加保存按钮 -->
			</div>
		</form>
	</div>
<?php
}
