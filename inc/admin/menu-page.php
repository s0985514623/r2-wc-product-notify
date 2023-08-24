<?php
// 创建后台菜单页面
function r2_notify_menu_page()
{
	add_menu_page(
		'課前提醒通知設定',       // 页面标题
		'課前提醒通知設定',       // 菜单标题
		'manage_options',       // 权限等级
		'r2-wc-product-notify',       // 菜单的slug
		'r2_notify_page_content' // 回调函数，用于输出页面内容
	);
}
add_action('admin_menu', 'r2_notify_menu_page');

// 后台页面的内容
function r2_notify_page_content()
{
	// 如果用户点击了保存按钮，则更新选项
	if (isset($_POST['r2_notify_save'])) {
		$days_before = sanitize_text_field($_POST['r2_notify_days_before']); // 获取表单提交的值
		update_option('r2_notify_days_before', $days_before); // 更新选项
		$clock_before = sanitize_text_field($_POST['r2_notify_clock_before']); // 获取表单提交的值
		update_option('r2_notify_clock_before', $clock_before); // 更新选项
		echo '<div class="updated"><p>設置已保存。</p></div>';
	}

?>
	<link rel="stylesheet" href="<?= plugins_url('r2-wc-product-notify/assets/css/r2-notify-page-content.css') ?>">
	<div class="pageWrap">
		<h2>課前提醒通知設定</h2>
		<form method="post" action="">
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
