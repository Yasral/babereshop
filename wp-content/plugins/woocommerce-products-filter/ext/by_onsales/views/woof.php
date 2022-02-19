<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$woof_ext_onsales_label = apply_filters('woof_ext_custom_title_by_onsales', esc_html__('On sale', 'woocommerce-products-filter'));
if (isset($WOOF->settings['by_onsales']) AND $WOOF->settings['by_onsales']['show']) {
    if (!isset($additional_taxes)) {
        $additional_taxes = "";
    }
    $_REQUEST['additional_taxes'] = $additional_taxes;
    $show_count = get_option('woof_show_count', 0);
    $show_count_dynamic = get_option('woof_show_count_dynamic', 0);
    $hide_dynamic_empty_pos = 0;
    $_REQUEST['hide_terms_count_txt'] = 0;
    $count_string = "";
    $count = 0;
    $current_request = $WOOF->get_request_data();

    if (!isset($current_request['onsales'])) {
        if ($show_count) {

            if ($show_count_dynamic) {

                $count = $WOOF->dynamic_count(array(), 'multi', $_REQUEST['additional_taxes'], array(), "onsale");
            } else {
                $all_ids = wc_get_product_ids_on_sale();
                $count = count($all_ids);
            }
            $count_string = '<span>(' . $count . ')</span>';
        }
        //+++
        if ($hide_dynamic_empty_pos AND $count == 0) {
            return "";
        }
    }


    ?>
    <div data-css-class="woof_checkbox_sales_container" class="woof_checkbox_sales_container woof_container woof_container_onsales">
        <div class="woof_container_overlay_item"></div>
        <div class="woof_container_inner">
            <input type="checkbox" class="woof_checkbox_sales" id="woof_checkbox_sales" name="sales" value="0" <?php checked('salesonly', $WOOF->is_isset_in_request_data('onsales') ? 'salesonly' : '', true) ?> />&nbsp;&nbsp;<label for="woof_checkbox_sales"><?php echo $woof_ext_onsales_label, $count_string ?></label><br />
        </div>
    </div>
    <?php
}


