<!DOCTYPE html>
<html lang="zh-TW">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>無限創意行銷</title>
</head>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<?php do_action('thwec_before_contents'); ?><?php if (!isset($order) && isset($gift_card->order_id)) {
																								$order = wc_get_order($gift_card->order_id);
																							} ?><?php if (isset($order) && is_a($order, 'WC_Order_Refund')) {
						$order = wc_get_order($order->get_parent_id());
					} ?>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>無限創意行銷</title>
	<?php do_action('thwec_before_contents'); ?><?php if (!isset($order) && isset($gift_card->order_id)) {
																								$order = wc_get_order($gift_card->order_id);
																							} ?><?php if (isset($order) && is_a($order, 'WC_Order_Refund')) {
						$order = wc_get_order($order->get_parent_id());
					} ?><table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="thwec_template_wrapper">
		<tr>
			<td align="center" class="thwec-template-wrapper-column" valign="top" style="background-color: #f7f7f7; padding: 70px 0;" bgcolor="#f7f7f7">
				<div id="thwec_template_container">
					<table id="tp_temp_builder" width="600" cellspacing="0" cellpadding="0" class="main-builder thwec-template-block" style="max-width: 600px; width: 600px; margin: auto; box-sizing: border-box;" align="center">
						<tr>
							<td class="thwec-builder-column" style="vertical-align: top; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: none; border-color: transparent; background-color: #fff; background-image: none; background-position: center; background-size: 100%; background-repeat: no-repeat;" bgcolor="#fff">
								<table class="thwec-row thwec-block-one-column builder-block" id="tp_1001" cellpadding="0" cellspacing="0px" style="width: 100%; table-layout: fixed; max-width: 100%; margin: 0 auto; border-spacing: 0px; padding-top: 12px; padding-right: 10px; padding-bottom: 12px; padding-left: 10px; margin-top: 0px; margin-right: auto; margin-bottom: 0px; margin-left: auto; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-style: dotted; border-color: #ddd; background-image: none; background-color: transparent; background-position: center; background-size: 100%; background-repeat: no-repeat;" width="100%" align="center" bgcolor="transparent">
									<tr>
										<td class="column-padding thwec-col thwec-columns" id="tp_1002" style="box-sizing: border-box; word-break: break-word; padding: 10px 10px; width: 100%; padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px; text-align: center; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-style: dotted; border-color: #ddd; background-image: none; background-color: transparent; background-position: center; background-size: 100%; background-repeat: no-repeat; vertical-align: top;" width="100%" align="center" bgcolor="transparent">
											<table class="thwec-block thwec-block-text builder-block" id="tp_1004" cellspacing="0" cellpadding="0" style='table-layout: fixed; margin: 0 auto; box-sizing: border-box; color: #636363; font-size: 13px; line-height: 22px; font-weight: normal; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; width: 100%; margin-top: 0px; margin-right: auto; margin-bottom: 0px; margin-left: auto; text-align: left;' align="center" width="100%">
												<tr style='color: #636363; font-size: 13px; font-weight: normal; line-height: 22px; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif;'>
													<td class="thwec-block-child thwec-block-text-holder" style='vertical-align: top; box-sizing: border-box; padding: 15px 15px; color: #636363; font-size: 13px; font-weight: normal; line-height: 22px; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; background-color: transparent; background-image: none; background-size: 100%; background-position: center; background-repeat: no-repeat; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-color: transparent; border-style: none; padding-top: 15px; padding-right: 15px; padding-bottom: 15px; padding-left: 15px; text-align: left;' bgcolor="transparent" align="left">
														<div class="wec-txt-wrap" style='color: #636363; font-size: 13px; font-weight: normal; line-height: 22px; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif;'>
															您好，感謝您報名了此堂課程<br style='color: #636363; font-size: 13px; font-weight: normal; line-height: 22px; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif;'>
														</div>
													</td>
												</tr>
											</table>
											<?php $obj = isset($order) && is_a($order, "WC_Order") ? $order : null;
											do_action('custom_hook_name', $obj, $email); ?>
											<table class="thwec-block thwec-block-text builder-block" id="tp_1007" cellspacing="0" cellpadding="0" style='table-layout: fixed; width: 100%; color: #636363; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; line-height: 22px; margin: 0 auto; box-sizing: border-box; text-align: left; font-weight: 700; font-size: 14px;' width="100%" align="center">
												<tr style="font-weight: 700; font-size: 14px;">
													<td class="thwec-block-child thwec-block-text-holder" style='vertical-align: top; box-sizing: border-box; color: #636363; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; line-height: 22px; padding: 15px 15px; background-color: transparent; background-size: 100%; background-repeat: no-repeat; background-position: center; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-color: transparent; border-style: none; text-align: left; font-weight: 700; font-size: 14px;' bgcolor="transparent" align="left">
														<div class="wec-txt-wrap" style="font-weight: 700; font-size: 14px;">
															課程須知<br style="font-weight: 700; font-size: 14px;">
														</div>
													</td>
												</tr>
											</table>
											<table class="thwec-block thwec-block-text builder-block" id="tp_1008" cellspacing="0" cellpadding="0" style='table-layout: fixed; width: 100%; color: #636363; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 13px; line-height: 22px; margin: 0 auto; box-sizing: border-box; text-align: left;' width="100%" align="center">
												<tr>
													<td class="thwec-block-child thwec-block-text-holder" style='vertical-align: top; box-sizing: border-box; color: #636363; font-family: "Helvetica Neue",Helvetica,Roboto,Arial,sans-serif; font-size: 13px; line-height: 22px; padding: 15px 15px; background-color: transparent; background-size: 100%; background-repeat: no-repeat; background-position: center; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-color: transparent; border-style: none; text-align: left;' bgcolor="transparent" align="left">
														<div class="wec-txt-wrap">1.上課嚴禁錄音及錄影，請各位學員遵守規範<br>
														</div>
														<div class="wec-txt-wrap">2.上課時請將手機等裝置調至靜音或震動，如需接聽電話請移步至教室外<br>
														</div>
														<div class="wec-txt-wrap">3.現場僅備有少量延長線，若擔心電源不足，請記得自備延長線唷<br>
														</div>
														<div class="wec-txt-wrap">
															<u><b>課程小提醒</b>：最後，由於課程緊湊，建議您可先準備相關問題，於下課時發問。</u><br>
														</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</body>

</html>