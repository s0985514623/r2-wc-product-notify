// Add a new attribute (via ajax).
jQuery(function ($) {
  // WC AJAX 函式
  const ajaxFunction = () => {
    var $wrapper = $("button.r2_add_new_attribute").closest(".woocommerce_attribute");
    var attribute = $wrapper.data("taxonomy");
    var new_attribute_name = $("#datepickerInput").val();

    if (new_attribute_name) {
      var data = {
        action: "woocommerce_add_new_attribute",
        taxonomy: attribute,
        term: new_attribute_name,
        security: woocommerce_admin_meta_boxes.add_attribute_nonce,
      };

      $.post(woocommerce_admin_meta_boxes.ajax_url, data, function (response) {
        if (response.error) {
          // Error.
          window.alert(response.error);
        } else if (response.slug) {
          // Success.

          $wrapper.find("select.attribute_values").append('<option value="' + response.term_id + '" selected="selected">' + response.name + "</option>");
          $wrapper.find("select.attribute_values").trigger("change");
        }

        $(".product_attributes").unblock();
      });
    } else {
      $(".product_attributes").unblock();
    }
  };

  const dialogInit = () => {
    // 初始化 Dialog
    $("#datepickerInput").val("");
    $("#datepickerDialog").dialog({
      title: "請選擇日期", // 標題
      autoOpen: false, // 初始時設置為不自動打開
      modal: true, // 背景變暗，禁止操作背後的內容
      buttons: {
        確認: function () {
          // 當按下 OK 按鈕時，獲取選擇的日期
          var selectedDate = $("#datepickerInput").val();
          $(this).dialog("close"); // 關閉 Dialog
        },
        取消: function () {
          $(this).dialog("close"); // 關閉 Dialog
        },
      },
      close: function () {
        // 在 Dialog 視窗關閉後，繼續執行你之前暫停的函數
        $(".product_attributes").block({
          message: null,
          overlayCSS: {
            background: "#fff",
            opacity: 0.6,
          },
        });
        ajaxFunction();
      },
    });
    // 套用 Datepicker 到 input 元素
    $("#datepickerInput").datepicker({
      dateFormat: "yy/mm/dd",
    });
  };

  $(".product_attributes").on("click", "button.r2_add_new_attribute", function (event) {
    dialogInit();
    // prevent form submission but allow event propagation
    event.preventDefault();
    $("#datepickerDialog").dialog("open");
  });

  // 解決Select選擇後無法儲存的問題
  $("#product_attributes").on("change", "select.attribute_values.r2-product-date", function () {
    // 選擇日期後，將日期填入dialog input元素
    $("#datepickerInput").val($(this).val());
    // 更新儲存按鈕屬性
    jQuery.maybe_disable_save_button();
  });

  $(document).on("click", "#preview-button", function (e) {
    e.preventDefault();
    const loading_spin = $(this).find(".loading-spin");
    loading_spin.show();
    const product_id = $(this).data("product_id") ?? null;
    const variation_id = $(this).data("variation_id") ?? null;
    const data = {
      action: "send_preview_mail",
      nonce: r2_wc_ajax_object.nonce,
      product_id, //簡單產品的ID
    };
    if (variation_id) {
      data.variation_id = variation_id; //變動產品的ID
    }
    $.ajax({
      type: "POST",
      url: r2_wc_ajax_object.ajax_url,
      data: data,
      success(res) {
        loading_spin.hide();
        return res;
      },
      error(error) {
        console.log("🚀 ~ error:", error);
        return error;
      },
    });
  });
});
