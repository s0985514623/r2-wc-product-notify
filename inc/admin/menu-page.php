<?php
// 创建后台菜单页面
function my_custom_menu_page()
{
	add_menu_page(
		'課前提醒通知設定',       // 页面标题
		'課前提醒通知設定',       // 菜单标题
		'manage_options',       // 权限等级
		'r2-wc-product-notify',       // 菜单的slug
		'my_custom_page_content' // 回调函数，用于输出页面内容
	);
}
add_action('admin_menu', 'my_custom_menu_page');

// 后台页面的内容
function my_custom_page_content()
{
	echo '<div class="wrap">';
	echo '<h2>My Custom Page</h2>';
	echo '<p>This is the content of my custom page.</p>';
	echo '</div>';
}
