<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
global $WOOF;

$request = $WOOF->get_request_data();
$current_request_txt = "";
if ($WOOF->is_isset_in_request_data("datepicker_" . $meta_key)) {
    $current_request_txt = $request["datepicker_" . $meta_key];
    $current_request = explode('-', urldecode($current_request_txt));
} else {
    $current_request = array();
}

//***

$from = "";
$to = "";
if (!empty($current_request)) {
    $from = ($current_request[0] != "i") ? $current_request[0] : "";
    $to = ($current_request[1] != "i") ? $current_request[1] : "";
}
//Check if datepicker has  products in current request
$count = 0;
$show = true;
$hide_dynamic_empty_pos = 0;
if (empty($current_request)) {
    if ($hide_dynamic_empty_pos) {
        $meta_field = array(
            'key' => $meta_key,
            'value' => array("i", "i"),
        );
        $count_data = array();
        $count = $WOOF->dynamic_count(array(), 'checkbox_ex', (isset($_REQUEST['additional_taxes'])) ? $_REQUEST['additional_taxes'] : "", $meta_field);
    }
    //+++
    if ($hide_dynamic_empty_pos AND $count == 0) {
        $show = false;
    }
}

$format = (isset($meta_settings['format'])) ? $meta_settings['format'] : "mm/dd/yy";
$show_title_label = (isset($meta_settings['show_title_label'])) ? $meta_settings['show_title_label'] : 1;
$css_classes = "woof_block_html_items";
$show_toggle = 0;
if (isset($meta_settings['show_toggle_button'])) {
    $show_toggle = (int) $meta_settings['show_toggle_button'];
}
//***
$block_is_closed = true;
if (!empty($current_request)) {
    $block_is_closed = false;
}
if ($show_toggle === 1 AND empty($current_request)) {
    $css_classes .= " woof_closed_block";
}

if ($show_toggle === 2 AND empty($current_request)) {
    $block_is_closed = false;
}
$tooltip_text = "";
if (isset($meta_settings['tooltip_text'])) {
    $tooltip_text = $meta_settings['tooltip_text'];
}
if (in_array($show_toggle, array(1, 2))) {
    $block_is_closed = apply_filters('woof_block_toggle_state', $block_is_closed);
    if ($block_is_closed) {
        $css_classes .= " woof_closed_block";
    } else {
        $css_classes = str_replace('woof_closed_block', '', $css_classes);
    }
}

if ($show):
    $top_panel_txt = "";
    $top_panel_txt = WOOF_HELPER::wpml_translate(null, $options['title']);

    $format_ = $format;
    $format_compatibility = array(
        'mm/dd/yy' => "m/d/y",
        'dd-mm-yy' => 'd-m-y',
        'yy-mm-dd' => 'y-m-d',
        'D, d M, yy' => 'D, d M, Y',
        'd MM, y' => 'd M, y',
    );

    if (isset($format_compatibility[$format_])) {
        $format_ = $format_compatibility[$format_];
    }
    if ($from) {
        $top_panel_txt .= " ";
        $top_panel_txt .= sprintf(esc_html__("from: %s", 'woocommerce-products-filter'), date($format_, $from));
    }
    if ($to) {
        $top_panel_txt .= " ";
        $top_panel_txt .= sprintf(esc_html__("to: %s", 'woocommerce-products-filter'), date($format_, $to));
    }
    ?>
    <div data-css-class="woof_meta_datepicker_container" class="woof_meta_datepicker_container woof_container woof_container_<?php echo "datepicker_" . $meta_key ?>">
        <div class="woof_container_inner">
            <div class="woof_container_inner woof_container_inner_datepicker_slider">
                <?php if ($show_title_label) :
                    ?>
                    <<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                    <?php echo WOOF_HELPER::wpml_translate(null, $options['title']) ?>
                    <?php echo WOOF_HELPER::draw_tooltipe(WOOF_HELPER::wpml_translate(null, $options['title']), $tooltip_text) ?>    
                    <?php WOOF_HELPER::draw_title_toggle($show_toggle, $block_is_closed); ?></<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                    <?php
                endif;
                ?>
                <div class="<?php echo $css_classes ?>">
                    <div class="woof_meta_datepicker_container">
                        <input class="woof_meta_datepicker_data" type="hidden" name="<?php echo $meta_key ?>_from" value="<?php echo $from ?>" />
                        <input data-format="<?php echo $format ?>" type="text" readonly="readonly" data-meta-key="<?php echo $meta_key ?>" class="woof_calendar woof_calendar_from" placeholder="<?php esc_html_e('from', 'woocommerce-products-filter') ?>" />
                        <a href="#" data-meta-key="<?php echo $meta_key ?>" data-name="<?php echo $meta_key ?>_from"  class="woof_meta_datepicker_reset">
                            <img src="<?php echo $WOOF->settings['delete_image'] ?>" height="12" width="12" alt="<?php esc_html_e("Сlear", 'woocommerce-products-filter') ?>" />
                        </a>
                    </div>
                    <div class="woof_meta_datepicker_container">
                        <input class="woof_meta_datepicker_data" type="hidden" name="<?php echo $meta_key ?>_to" value="<?php echo $to ?>" />
                        <input data-format="<?php echo $format ?>" type="text" readonly="readonly" data-meta-key="<?php echo $meta_key ?>" class="woof_calendar woof_calendar_to" placeholder="<?php esc_html_e('to', 'woocommerce-products-filter') ?>" />
                        <a href="#" data-meta-key="<?php echo $meta_key ?>"  data-name="<?php echo $meta_key ?>_to"   class="woof_meta_datepicker_reset">
                            <img src="<?php echo $WOOF->settings['delete_image'] ?>" height="12" width="12" alt="<?php esc_html_e("Clear", 'woocommerce-products-filter') ?>" />
                        </a>
                    </div>

                </div>
                <input type="hidden" value="<?php echo $top_panel_txt ?>" data-anchor="woof_n_<?php echo "datepicker_" . $meta_key ?>_<?php echo $current_request_txt ?>" />
            </div>
        </div>
    </div>
<?php endif; ?>
