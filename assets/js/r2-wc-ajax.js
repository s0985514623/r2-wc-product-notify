// Add a new attribute (via ajax).
(function ($) {
  $(".product_attributes").on(
    "click",
    "button.r2_add_new_attribute",
    function (event) {
      // prevent form submission but allow event propagation
      event.preventDefault();
      console.log("r2_add_new_attribute click");
      $(".product_attributes").block({
        message: null,
        overlayCSS: {
          background: "#fff",
          opacity: 0.6,
        },
      });

      var $wrapper = $(this).closest(".woocommerce_attribute");
      var attribute = $wrapper.data("taxonomy");
      var new_attribute_name = window.prompt(
        woocommerce_admin_meta_boxes.new_attribute_prompt
      );

      if (new_attribute_name) {
        var data = {
          action: "woocommerce_add_new_attribute",
          taxonomy: attribute,
          term: new_attribute_name,
          security: woocommerce_admin_meta_boxes.add_attribute_nonce,
        };

        $.post(
          woocommerce_admin_meta_boxes.ajax_url,
          data,
          function (response) {
            if (response.error) {
              // Error.
              window.alert(response.error);
            } else if (response.slug) {
              // Success.
              $wrapper
                .find("select.attribute_values")
                .append(
                  '<option value="' +
                    response.term_id +
                    '" selected="selected">' +
                    response.name +
                    "</option>"
                );
              $wrapper.find("select.attribute_values").trigger("change");
            }

            $(".product_attributes").unblock();
          }
        );
      } else {
        $(".product_attributes").unblock();
      }
    }
  );
})(jQuery);
